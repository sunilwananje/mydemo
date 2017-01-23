<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Model\Permission;
use App\Model\Menu;
use Session;

class MenuController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        //$d = new Menu();
        //dd($d->allMenu);
        return view('masters.menu.menuView');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    	$permissionData = Permission::where('status',1)->whereNotIn('id', function($query){
						    $query->select('permission_id')->from(with(new Menu)->getTable());
						  })->get();
        return view('masters.menu.menuCreate',compact('permissionData'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $menu = new Menu();
    	$res = $menu->validateMenu($request->all());

    	if ($res->fails()) {
            return redirect()->route('menu.create')
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $menu->saveMenu($request->all());
        	if($saveYN){
        		Session::flash('message','Menu Added Successfully');
        		return redirect()->route('menu.index');
        	}
        	else{

        		Session::flash('error','Menu Not Added');
        		return redirect()->route('menu.index');
        	}
        	
        }
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
        $recId = Menu::findOrFail($id);
    	/*$permissionData = Permission::whereNotIn('id', function($query){
						    $query->select('permission_id')->from(with(new Menu)->getTable());
						  })->get();*/
    	return view('masters.menu.menuEdit',compact('recId'));
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
        $menu = new Menu();
    	$res = $menu->validateMenu($request->all());

    	if ($res->fails()) {
            return redirect()->route('menu.edit',$id)
                        ->withErrors($res)
                        ->withInput();
        }
        else{

        	$saveYN = $menu->updateMenu($request->all(), $id);
        	if($saveYN){

        		Session::flash('message','Menu Updated Successfully');
        		return redirect()->route('menu.index');
        	}
        	else{

        		Session::flash('error','Menu Not Updated');
        		return redirect()->route('menu.index');	
        	}
        	
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $recId = Menu::findOrFail($id);

    	$recId->delete();
    	    	
    	if($recId){
    		Session::flash('message','Menu Deleted');
    		return redirect()->route('menu.index');
    	}
    	else{
    		Session::flash('error','menu Not Deleted');
    		return redirect()->route('menu.index');	
    	}
    }

    public function allPermissions() {
        return view('masters.menu.permissionsView');
    }
    public function permissionStatus($id,$status) {
        Permission::where('id', $id)
            ->update(['status' => $status]);
        return view('masters.menu.permissionsView');
    }
}
