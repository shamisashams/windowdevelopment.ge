<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApartmentRequest;
use App\Http\Requests\Admin\ProductRequest;
use App\Http\Requests\Admin\SettingRequest;
use App\Models\Apartment;
use App\Models\Category;
use App\Models\Floor;
use App\Models\Product;
use App\Models\Setting;
use App\Repositories\ApartmentRepositoryInterface;
use App\Repositories\CategoryRepositoryInterface;
use App\Repositories\FloorRepositoryInterface;
use App\Repositories\ProductRepositoryInterface;
use App\Repositories\SettingRepositoryInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;

class FloorController extends Controller
{

    private $floorRepository;


    public function __construct(
        FloorRepositoryInterface  $floorRepository
    )
    {
        $this->floorRepository = $floorRepository;
    }


    /**
     * @param SettingRequest $request
     * @return Application|Factory|View
     */
    public function index(SettingRequest $request)
    {
        return view('admin.pages.floor.index', [
            'floors' => $this->floorRepository->getData($request, ['translations'])
        ]);
    }


    /**
     * @param string $locale
     * @param Setting $setting
     * @return Application|Factory|View
     */
    public function show(string $locale, Apartment $apartment)
    {
        return view('admin.pages.apartment.show', [
            'apartment' => $apartment,
        ]);
    }


    /**
     * @param string $locale
     * @param Setting $setting
     * @return Application|Factory|View
     */
    public function edit(string $locale, Floor $floor)
    {
        $url = locale_route('floor.update', $floor->id, false);
        $method = 'PUT';

        return view('admin.pages.floor.form', [
            'floor' => $floor,
            'url' => $url,
            'method' => $method,
            'apartments' => Apartment::get(),
        ]);
    }


    /**
     * @param SettingRequest $request
     * @param string $locale
     * @param Setting $setting
     * @return Application|RedirectResponse|Redirector
     */
    public function update(Request $request, string $locale, Floor $floor)
    {
        $saveData = Arr::except($request->except('_token'), []);
        $saveData['status'] = isset($saveData['status']) && (bool)$saveData['status'];
        $this->floorRepository->update($floor->id,$saveData);
        $this->floorRepository->saveFiles($floor->id, $request);



        return redirect(locale_route('apartment.show', $floor->id))->with('success', __('admin.update_successfully'));
    }
}
