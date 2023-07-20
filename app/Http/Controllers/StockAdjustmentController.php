<?php
/*
* ProBot Version: 3.0
* Laravel Version: 10x
* Description: This source file "app/Http/_StockAdjustmentController.php" was generated by ProBot AI.
* Date: 5/9/2023 12:57:18 AM
* Contact: towhid1@outlook.com
*/
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\StockAdjustment;
use App\Models\User;
use App\Models\Adjustment_Type;
use App\Models\Product;
use App\Models\StockAdjustmentDetail;
use App\Models\StockAdjustmentType;
use App\Models\Warehouse;
use App\Models\Werehouse;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
class StockAdjustmentController extends Controller{
	public function index(){
		$stockadjustments = DB::table("stock_adjustments as s")
		->join("users as u","s.user_id","=","u.id")
		->join("stock_adjustment_types as t","s.adjustment_type_id","=","t.id")
		->join("warehouses as w","s.werehouse_id","=","w.id")
		->select("s.*","u.username as uname","t.name as tname","w.name as wname")
		->paginate(5);
		return view("pages.erp.stockadjustment.index",["stockadjustments"=>$stockadjustments]);
	}
	public function create(){
		return view("pages.erp.stockadjustment.create",["users"=>User::all(),"adjustment_types"=>StockAdjustmentType::all(),"werehouses"=>Warehouse::all(),"products"=>Product::all()]);
	}
	public function store(Request $request){
		//StockAdjustment::create($request->all());
		$stockadjustment = new StockAdjustment;
		$stockadjustment->adjustment_at=date("Y-m-d H:i:s",strtotime($request->txtAdjustDate));
		$stockadjustment->user_id=$request->cmbUser;
		$stockadjustment->remark=$request->txtremark;
		$stockadjustment->adjustment_type_id=$request->txtAdtypeid;
		$stockadjustment->werehouse_id=$request->txtwarehouse;

		$stockadjustment->save();

		$type_details=$request->txtProducts;

		foreach($type_details as $type_detail){

			$detaile_type=new StockAdjustmentDetail;

			$detaile_type->adjustment_id=$stockadjustment->id;
			$detaile_type->product_id=$type_detail["item_id"];
			$detaile_type->measure=$type_detail["measure"];
			$detaile_type->price=$type_detail["price"];
			$detaile_type->uom_id=$type_detail["uom_id"];

			$detaile_type->save();

		}

		return back()->with('success', 'Created Successfully.');
	}
	public function show($id){
		$stockadjustment = StockAdjustment::find($id);
		$users=User::find($stockadjustment->user_id);

		$detail_adjustment=DB::table("stock_adjustment_details as s")
		->join("products as p","s.product_id","=","p.id")
		->join("uoms as u","s.uom_id","=","u.id")
		->where("s.adjustment_id",$id)
		->select("s.*","p.name as pname","u.name as uname")
		->get();

		return view("pages.erp.stockadjustment.show",["stockadjustment"=>$stockadjustment,"user"=>$users,"detail_adjustments"=>$detail_adjustment]);
	}
	public function edit(StockAdjustment $stockadjustment){
		return view("pages.erp.stockadjustment.edit",["stockadjustment"=>$stockadjustment,"users"=>User::all(),"adjustment_types"=>StockAdjustmentType::all(),"werehouses"=>Warehouse::all()]);
	}
	public function update(Request $request,StockAdjustment $stockadjustment){
		//StockAdjustment::update($request->all());
		$stockadjustment = StockAdjustment::find($stockadjustment->id);
		$stockadjustment->adjustment_at=$request->adjustment_at;
		$stockadjustment->user_id=$request->user_id;
		$stockadjustment->remark=$request->remark;
		$stockadjustment->adjustment_type_id=$request->adjustment_type_id;
		$stockadjustment->werehouse_id=$request->werehouse_id;

		$stockadjustment->save();

		return redirect()->route("stockadjustments.index")->with('success','Updated Successfully.');
	}
	public function destroy(StockAdjustment $stockadjustment){
		$stockadjustment->delete();
		return redirect()->route("stockadjustments.index")->with('success', 'Deleted Successfully.');
	}
}
?>