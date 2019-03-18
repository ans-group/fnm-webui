<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Actions;
use App\HostGroup;

class ActionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {
        if(isset($_GET['ip']) && !is_null($_GET['ip']) && !empty($_GET['ip'])) {
            $actions = Actions::orderBy('id', 'desc')->where('ip', $_GET['ip'])->paginate(20);
            $filtered = true;
        } elseif(isset($_GET['dc']) && !is_null($_GET['dc']) && !empty($_GET['dc']) && is_numeric($_GET['dc'])) {
            $actions = Actions::orderBy('id', 'desc')->where('dc_id', $_GET['dc'])->paginate(20);
            $filtered = true;
        } elseif(isset($_GET['uuid']) && !is_null($_GET['uuid']) && !empty($_GET['uuid'])) {
            $actions = Actions::orderBy('id', 'desc')->where('uuid', $_GET['uuid'])->paginate(20);
            $filtered = true;
        } else {
            $actions = Actions::orderBy('id', 'desc')->paginate(20);
            $filtered = false;
        }
        return view('action.index')->with('actions', $actions)->with('filtered', $filtered);
    }

    public function show(Actions $action) {
        $similar = Actions::where([
                ['uuid', '=', $action->uuid],
                ['id', "<>", $action->id],
            ])->orWhere([
                ['ip', '=', $action->ip],
                ['id', "<>", $action->id],
            ])->orderBy('id', 'desc')->take(6)->get();
        return view('action.show')->with('action', $action)->with('similar', $similar);
    }
}
