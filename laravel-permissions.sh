#!/usr/bin/env bash
# Rocky Linux 9 / Nginx / PHP-FPM (nginx:nginx).
# Place in the Laravel project root, then run: sudo bash laravel-permissions.sh
# Extra writable directories (relative to project):
#   sudo bash laravel-permissions.sh public/user_documents
# Optional deployment owner: sudo env APP_OWNER=development bash laravel-permissions.sh
# Code becomes OWNER:nginx, readable by nginx but writable only by OWNER.
# Existing executable files retain execute permission. Symlinks are not followed.
# This script does not run Artisan, change Nginx config, or restart services.
set -Eeuo pipefail
trap 'printf "ERROR: failed at line %s. Some changes may already be applied.\n" "$LINENO" >&2' ERR

die() { printf 'ERROR: %s\n' "$*" >&2; exit 1; }
if [[ ${1:-} == --help || ${1:-} == -h ]]; then
    printf '%s\n' 'Usage: sudo bash laravel-permissions.sh [relative-upload-directory ...]' \
        'Run from a copy placed in the Laravel project root.' \
        'Example: sudo bash laravel-permissions.sh public/user_documents' \
        'Optional environment: APP_OWNER=root WEB_USER=nginx WEB_GROUP=nginx'
    exit 0
fi
[[ $EUID -eq 0 ]] || die 'Run as root or with sudo.'
APP_DIR=$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd -P)
APP_OWNER=${APP_OWNER:-root}
WEB_USER=${WEB_USER:-nginx}
WEB_GROUP=${WEB_GROUP:-nginx}
[[ $APP_DIR != / && -f "$APP_DIR/artisan" && -f "$APP_DIR/composer.json" && -d "$APP_DIR/public" && -d "$APP_DIR/bootstrap" ]] ||
    die 'Place this script in the Laravel project root (beside artisan and composer.json).'
id "$APP_OWNER" >/dev/null || die 'APP_OWNER does not exist.'
id "$WEB_USER" >/dev/null || die 'WEB_USER does not exist.'
getent group "$WEB_GROUP" >/dev/null || die 'WEB_GROUP does not exist.'

# Validate every writable path before making any changes.
WRITABLE=("$APP_DIR/storage" "$APP_DIR/bootstrap/cache")
for relative in "$@"; do
    [[ $relative != /* && $relative != -* && -n $relative ]] || die 'Upload paths must be relative to the project.'
    target=$(realpath -m -- "$APP_DIR/$relative")
    [[ $target == "$APP_DIR/public/"* ]] || die 'Extra writable paths must be subdirectories of public, e.g. public/user_documents.'
    WRITABLE+=("$target")
done
for target in "${WRITABLE[@]}"; do
    [[ $(realpath -m -- "$target") == "$target" ]] || die "Symlinked runtime path needs manual configuration: $target"
    [[ ! -e $target || -d $target ]] || die "Expected a directory: $target"
done

printf 'Project: %s\nCode owner: %s\nPHP-FPM identity: %s:%s\n' "$APP_DIR" "$APP_OWNER" "$WEB_USER" "$WEB_GROUP"
if ! command -v setfacl >/dev/null || ! command -v semanage >/dev/null; then
    dnf install -y acl policycoreutils-python-utils
fi
for tool in setfacl semanage restorecon runuser; do
    command -v "$tool" >/dev/null || die "Required command missing: $tool"
done

# find defaults to not following symlinks, including public/storage.
# Reset ACLs so old named-user grants cannot leave code writable.
printf 'Setting application permissions...\n'
find "$APP_DIR" -type d -exec setfacl -b -k -- {} +
find "$APP_DIR" -type f -exec setfacl -b -- {} +
find "$APP_DIR" \( -type d -o -type f \) -exec chown "$APP_OWNER:$WEB_GROUP" -- {} +
find "$APP_DIR" -type d -exec chmod 2750 -- {} +
find "$APP_DIR" -type f -exec chmod 'u=rwX,g=rX,o=' -- {} +
if [[ -f "$APP_DIR/.env" && ! -L "$APP_DIR/.env" ]]; then
    chmod 640 -- "$APP_DIR/.env"
fi

for target in "${WRITABLE[@]}"; do
    mkdir -p -- "$target"
    find "$target" \( -type d -o -type f \) -exec chown "$APP_OWNER:$WEB_GROUP" -- {} +
    find "$target" -type d -exec chmod 2770 -- {} +
    find "$target" -type f -exec chmod 660 -- {} +
    find "$target" -type d -exec setfacl -m \
        "u:$WEB_USER:rwx,m::rwx,d:u::rwx,d:u:$WEB_USER:rwx,d:g::rwx,d:m::rwx,d:o::---" -- {} +
    find "$target" -type f -exec setfacl -m "u:$WEB_USER:rw-,m::rw-" -- {} +
done
mkdir -p -- "$APP_DIR/storage/logs" "$APP_DIR/storage/framework/cache/data" \
    "$APP_DIR/storage/framework/sessions" "$APP_DIR/storage/framework/views"

# Escape regex metacharacters in paths used by semanage.
context_rule() {
    local escaped
    escaped=$(printf '%s' "$1" | sed 's/[][\\.^$*+?(){}|]/\\&/g')
    semanage fcontext -a -t "$2" "${escaped}(/.*)?" ||
        semanage fcontext -m -t "$2" "${escaped}(/.*)?"
}
if command -v selinuxenabled >/dev/null && selinuxenabled; then
    printf 'Applying persistent SELinux labels...\n'
    context_rule "$APP_DIR" httpd_sys_content_t
    for target in "${WRITABLE[@]}"; do
        context_rule "$target" httpd_sys_rw_content_t
    done
    restorecon -R "$APP_DIR"
else
    printf 'SELinux is disabled; labeling skipped. Rerun after enabling it.\n'
fi

printf 'Checking PHP-FPM user access...\n'
runuser -u "$WEB_USER" -- test -r "$APP_DIR/public/index.php"
for target in "${WRITABLE[@]}"; do
    runuser -u "$WEB_USER" -- bash -c \
        'set -e; probe=$(mktemp "$1/.permission-check.XXXXXX"); rm -- "$probe"' bash "$target"
    printf 'Write OK: %s\n' "$target"
done
printf '\nDone. No restart required.\n'
printf 'Access checks verify Unix permissions; verify the site to confirm SELinux access.\n'
printf 'Run Artisan and queue workers as %s to keep runtime ownership consistent.\n' "$WEB_USER"
