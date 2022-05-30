<?php

namespace App\Http\Controllers;

use App\Models\GeoCity;
use Illuminate\Http\Request;

class CityController extends Controller
{
    private function getRussianName(string $str, ?string $search)
    {
        if (!$str) {
            return '';
        }

        $strArray = explode(",", $str);

        if ($search) {
            foreach ($strArray as $name) {
                if (strrpos($name, $search) !== false) {
                    return $name;
                }
            }
        } else {
            foreach ($strArray as $name) {
                if (preg_match('/[А-Я][а-я]/u', $name)) {
                    return $name;
                }
            }
        }

        return '';
    }

    public function index(Request $request)
    {
        $search = $request->input('search', null);
        $cities = GeoCity::where('country_code', 'RU')->where('population', '>', 100000)->where('alternate_names', '!=', '');
        if ($search) {
            $cities = $cities->where('alternate_names', 'like', '%' . $search . '%');
        }
        $cities = $cities->limit(120)->get();

        $cities->map(function ($el) use ($search) {
            if ($el->alternate_names) {
//                $el->name = $this->getRussianName($el->alternate_names, $search);
            }

            return $el;
        });

        return response()->json($cities);
    }
}
