<?php

namespace App\SuperAdmin\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Models\Company;
use App\Models\Lang;
use App\SuperAdmin\Http\Requests\Api\Company\GlobalCompanyRequest;
use App\SuperAdmin\Models\GlobalCompany;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;

class GlobalCompanyController extends ApiBaseController
{
    protected $model = GlobalCompany::class;

    protected $updateRequest = GlobalCompanyRequest::class;

    public function updating(GlobalCompany $company)
    {
        return $company;
    }

    public function updated(GlobalCompany $company)
    {
        $user = user();

        $company = Company::withoutGlobalScope('company')->with(['warehouse' => function ($query) use ($user) {
            return $query->withoutGlobalScope(CompanyScope::class)
                ->where('company_id', $user->company_id);
        }, 'currency' => function ($query) use ($user) {
            return $query->withoutGlobalScope(CompanyScope::class)
                ->where('company_id', $user->company_id);
        }])->where('id', $user->company_id)->first();

        session(['company' => $company]);

        return $company;
    }

    public function getWebsiteLang()
    {
        $websiteLang = GlobalCompany::select('website_lang_id')->first();

        if ($websiteLang) {
            if ($websiteLang->website_lang_id && $websiteLang->website_lang_id != null) {
                $langDetails = Lang::find($websiteLang->website_lang_id);

                $lang = $langDetails ? $langDetails : Lang::where('key', 'en')->first();
            } else {
                $lang = Lang::where('key', 'en')->first();
            }
        } else {
            $lang = Lang::where('key', 'en')->first();
        }

        return ApiResponse::make('Success', [
            'lang' => $lang,
        ]);
    }
}
