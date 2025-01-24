<?php

namespace App\Http\Controllers\Languages;

use App\Http\Controllers\Controller;
use App\Models\Languages;
use App\Library\Utilities;

use Illuminate\Http\Request;

class LanguageTranslationController extends Controller
{
    public static $page_title = 'Language Translations';
    public static $menu_dtl_id = '337';
    public static $redirect_url = 'languages';

    public function create(Request $request, $id = null)
    {
        $data['page_data'] = [];
        $data['form_type'] = 'languages';
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage . self::$redirect_url;
        $data['page_data']['create'] = '/' . self::$redirect_url . $this->prefixCreatePage;

        if (!isset($id) || !Languages::where('code', $id)->exists()) {
            abort(404, 'Language not found.');
        }

        $data['permission'] = self::$menu_dtl_id . '-edit';
        $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
        $data['id'] = $id;
        $data['current'] = Languages::where('code', $id)->first();

        if (!$data['current']) {
            abort(404, 'Language record not found.');
        }

        $langPath = base_path('resources/lang/' . $id . '/message.php');

        // Check if the language file exists
        if (!file_exists($langPath)) {
            abort(404, "Language file for '{$id}' not found.");
        }

        // Read existing translations
        $full_data = include($langPath);
        $full_data = array_filter($full_data, fn($value) => !is_null($value) && $value !== '');
        ksort($full_data);

        // Paginate translations for display
        $data['translations'] = $this->convertArrayToCollection($id, $full_data, config('default_pagination'));

        // Handle form submission for adding/updating translations
        if ($request->isMethod('post')) {
            $key = $request->input('key');
            $value = $request->input('value');

            // Validate input
            if (empty($key) || empty($value)) {
                return redirect()->back()->withErrors('Both key and value are required.');
            }

            // Update or add the new translation
            $full_data[$key] = $value;

            // Save updated translations back to the file
            $exportedTranslations = var_export($full_data, true);
            file_put_contents($langPath, "<?php\n\nreturn {$exportedTranslations};");

            return redirect()->back()->with('success', 'Translation added/updated successfully.');
        }

        return view('settings.languages.form', compact('data'));
    }
}
