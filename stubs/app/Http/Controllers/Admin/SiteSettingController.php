<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schema;
use App\Models\SiteSettings;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SiteSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): Response
    {
        $schema = Schema::where('name', 'settings')->get()->first();

        $data = [
            'schema' => $schema->toArray(),
        ];

        return Inertia::render('Admin/Settings', $data);
    }


    /**
     * settings.create - create a new setting
     * @return \Inertia\Response
     */
    public function create(Request $request)
    {
        // get the setting from the request
        $setting = $request->validate([
            'name' => 'required|string',
            'value' => 'required',
        ]);
        // create the setting
        SiteSettings::create($setting);

        return Inertia::render('Setting/Create');
    }

    //setting.store - POST - /settings

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // store the setting
        $setting = SiteSettings::create($request->validate([
            'name' => 'required',
            'value' => 'required',
        ]));
        // redirect back with data
        return redirect()->back();
    }


    //setting.show
    public function show(SiteSettings $setting)
    {
        return Inertia::render('Setting/Show', [
            'setting' => $setting,
        ]);
    }

    //setting.edit
    public function edit(SiteSettings $setting)
    {
        return Inertia::render('Setting/Edit', [
            'setting' => $setting,
        ]);
    }

    //setting.update
    public function update(Request $request, SiteSettings $siteSetting): SiteSettings
    {
        $data = $request->validate([
            'siteSetting.name' => 'required|string',
            'siteSetting.value' => 'required',
        ])['siteSetting'];


        $siteSetting->update($data);


        return $siteSetting;
    }

    //setting.destroy

    /**
     * @param SiteSettings $setting
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(SiteSettings $setting)
    {
        $setting->delete();
        return redirect()->back();
    }

}


