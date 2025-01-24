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
    // Initialize page data
    $data['page_data'] = [];
    $data['form_type'] = 'languages';
    $data['page_data']['title'] = self::$page_title;
    $data['page_data']['path_index'] = $this->prefixIndexPage . self::$redirect_url;
    $data['page_data']['create'] = '/' . self::$redirect_url . $this->prefixCreatePage;

    // Validate language existence
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

    if (!file_exists($langPath)) {
        abort(404, "Language file for '{$id}' not found.");
    }

    $translations = include($langPath);
    $translations = array_filter($translations, fn($value) => !is_null($value) && $value !== '');
    ksort($translations);

    if ($request->isMethod('post')) {
        $translations = $request->input('translations');

        if ($translations) {
            $langPath = base_path('resources/lang/' . $id . '/message.php');

            // Read existing translations
            $full_data = include($langPath);

            // Update existing translations with the new data
            foreach ($translations as $translation) {
                $full_data[$translation['key']] = $translation['value'];
            }

            // Save the updated translations back to the file
            $exportedTranslations = var_export($full_data, true);
        file_put_contents($langPath, "<?php\n\nreturn {$exportedTranslations};");

        return redirect()->back()->with('success', trans('message.update'), 200);
        }
        // return redirect()->back()->with('success', 'Translation added/updated successfully.');
    }

    // Paginate translations for display
    $perPage = config('default_pagination', 10); // Default items per page
    $currentPage = $request->input('page', 1);
    $paginatedTranslations = collect($translations)
        ->forPage($currentPage, $perPage)
        ->all();

    $data['translations'] = new \Illuminate\Pagination\LengthAwarePaginator(
        $paginatedTranslations,
        count($translations),
        $perPage,
        $currentPage,
        ['path' => $request->url()]
    );

    return view('setting.translations.form', compact('data'));
}


public function changeLanguage(Request $request)
{
    // $request->validate([
    //     'language' => 'required|string|exists:tbllanguages,id',
    // ]);

    $language = \App\Models\Languages::find($request->language);


    session(['app_locale' => $language->code]);

    app()->setLocale($language->code);

    return redirect()->back()->with('success', 'Language changed successfully!');
}


}
