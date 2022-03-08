<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\ML;

use App\Entities\ML\TrainModelItem;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Class TrainModelItemController
 * @package App\Http\Controllers\Admin\ML
 */
class TrainModelItemController extends Controller
{
    public function index()
    {
        return view('admin.ml.index');
    }

    public function store(Request $request)
    {
        $request->merge([
            'image' => $request->file->store('images/train')
        ]);

        TrainModelItem::create($request->all());

        return back();
    }
}
