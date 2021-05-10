<?php

namespace App\Http\Controllers;

use App\Product;
use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        $product = Product::latest()->paginate(5);

        return view('backend.product.index',[
          'data' => $product
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $data = Product::all();
        $categories = Category::all();

        return view('backend.product.create',[
            'categories' => $categories,
            'data' => $data
        ]);


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Kiểm tính đúng đắn của dữ liệu
        $request->validate([
            'name' => 'required|max:255',

        ],[
            'name.required' => 'Bạn cần phải nhập vào tên sản phẩm',

        ]);

        $product = new Product();

        $name = $request->input('name'); // tên sp
        $stock = (int)$request->input('stock'); // số lượng sp
        $price = $request->input('price'); // giá sp
        $sale = $request->input('sale'); // giá sale
        $position= $request->input('position');
        $category_id= $request->input('category_id');

        $slug = Str::slug($name);

        $path_image = '';
        if ($request->hasFile('image')) {
            // get file
            $file = $request->file('image');
            // get ten
            $filename = $file->getClientOriginalName();
            // duong dan upload
            $path_upload = 'uploads/product/';
            // upload file
            $file->move($path_upload,$filename);
            $path_image = $path_upload.$filename;
        }

        $is_active = 0;
        if ($request->has('is_active')) { // kiem tra is_active co ton tai khong?
            $is_active = $request->input('is_active');
        }

        $is_hot = 0;
        if ($request->has('is_hot')) { // kiem tra co phai sp noi bat ko
            $is_hot = $request->input('is_hot');
        }


        $product->name = $name;
        $product->price = $price;
        $product->sale = $sale;
        $product->stock = $stock;
        $product->position = $position;
        $product->category_id = $category_id;
        $product->image = $path_image;
        $product->slug = $slug;

        $product->save();

        // chuyen dieu huong trang
        return redirect()->route('admin.product.index');
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit($id)
    {
        $product = Product::findorFail($id);
        $categories = Category::all();

        return view('backend.product.edit', [
            'data' => $product,
            'categories' => $categories
        ]);
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
        $request->validate([
            'name' => 'required|max:255',

            //   'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:10000'
        ],[
            'name.required' => 'Bạn cần phải nhập vào tên sản phẩm',
            //   'parent_id.required' => 'Bạn cần phải nhập danh mục cha',

            //   'image.image' => 'File ảnh phải có dạng jpeg,png,jpg,gif,svg',
        ]);

        $name = $request->input('name'); // tên sp
        $stock = (int)$request->input('stock'); // số lượng sp
        $price = $request->input('price'); // giá sp
        $sale = $request->input('sale'); // giá sale
        $position= $request->input('position');
        $summary= $request->input('summary');
        $description= $request->input('description');
        $category_id= $request->input('category_id');
        $slug = Str::slug($name);


        if ($request->hasFile('image')) {
            // get file
            $file = $request->file('image');
            // get ten
            $filename = $file->getClientOriginalName();
            // duong dan upload
            $path_upload = 'uploads/category/';
            // upload file
            $file->move($path_upload,$filename);
            $path_image = $path_upload.$filename;
        }

        $is_active = 0;
        if ($request->has('is_active')) { // kiem tra is_active co ton tai khong?
            $is_active = $request->input('is_active');
        }

        $is_hot = 0;
        if ($request->has('is_hot')) { // kiem tra co phai sp noi bat ko
            $is_hot = $request->input('is_hot');
        }

        $product = Product::find($id);
        $product->name = $name;
        $product->price = $price;
        $product->sale = $sale;
        $product->stock = $stock;
        $product->position = $position;
        $product->summary = $summary;
        $product->description = $description;
        $product->category_id = $category_id;
        $product->slug = $slug;
        $product->save();

        // chuyen dieu huong trang
        return redirect()->route('admin.product.index');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $isDelete = Product::destroy($id);
        if ($isDelete) {
            return response()->json(['success' => 1, 'message' => 'Thành công']);
        } else {
            return response()->json(['success' => 0, 'message' => 'Thất bại']);
        }
    }
}
