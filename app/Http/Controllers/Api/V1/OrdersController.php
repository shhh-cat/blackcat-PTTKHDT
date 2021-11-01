<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Order;
use Carbon\Carbon;
class OrdersController extends Controller
{
    function __construct() 
    {
        $this->middleware('api.role:ordermanager');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::whereNotNull('request_date')
            ->whereNull('get_date')
            ->whereNull('ship_date')
            ->whereNull('completion_date')->get();
        $orders->map(function ($order)
                {
                    $order->late_level = $order->getLateLevel();
                });
        // chen permission vao day
        return $orders;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //USE VUEJS
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $user = User::create([
        //     'fname' => $request->fname,
        //     'lname' => $request->lname,
        //     'gender' => $request->gender,
        //     'email' => $request->email,
        //     'isAdmin' => $request->isAdmin,
        //     'password' => Hash::make(1),
        // ]);
        // if ($request->admin) $request->permissions->append(['id' => 1]);
        // //if ($request->permissions != null) 
        // $user->permissions()->sync($request->permissions);
        
        // return $user;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //USE VUEJS
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['get_date' => Carbon::now()]);
        
        return $order;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $user = User::findOrFail($id);
        // $user->permissions()->detach();
        // $user->delete();
        // return '';
    }
}
