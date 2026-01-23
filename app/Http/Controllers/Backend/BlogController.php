<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class BlogController extends Controller
{
    public function BlogCategory()
    {
        $category = BlogCategory::latest()->get();
        return view('admin.backend.blogcategory.blog_category', compact('category'));
    }

    public function StoreBlogCategory(Request $request)
    {
        BlogCategory::insert([
            'category_name' => $request->category_name,
            'category_slug' => strtolower(str_replace(' ', '-', $request->category_name)),
        ]);

        $notification = array(
            'message' => 'Blog Category Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function EditBlogCategory($id)
    {
        $categories = BlogCategory::find($id);
        return response()->json($categories);
    }

    public function UpdateBlogCategory(Request $request)
    {   
        $cat_id = $request->cat_id;
        BlogCategory::find($cat_id)->update([
            'category_name' => $request->category_name,
            'category_slug' => strtolower(str_replace(' ', '-', $request->category_name)),
        ]);

        $notification = array(
            'message' => 'Blog Category Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function DeleteBlogCategory($id)
    {
        BlogCategory::find($id)->delete();

        $notification = array(
            'message' => 'Blog Category Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function AllBlogPost()
    {
        $post = BlogPost::latest()->get();
        return view('admin.backend.blogpost.all_post', compact('post'));
    }

    public function AddBlogPost()
    {
        $blogcat = BlogCategory::latest()->get();
        return view('admin.backend.blogpost.add_post', compact('blogcat'));
    }

    public function StoreBlogPost(Request $request)
    {

        if ($request->file('image')) {
            $image = $request->file('image');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()) . '.' .
                $image->getClientOriginalExtension();
            $img = $manager->read($image->getRealPath());
            $img->resize(746, 500)->save(public_path('upload/post/' . $name_gen));
            $save_url = 'upload/post/' . $name_gen;

            BlogPost::create([
                'blogcat_id' => $request->blogcat_id,
                'post_title' => $request->post_title,
                'post_slug' => strtolower(str_replace(' ', '-', $request->post_title)),
                'long_descp' => $request->long_descp,
                'image' => $save_url,

            ]);
        }

        $notification = array(
            'message' => 'Blog Post Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.blog.post')->with($notification);
    }

    public function EditBlogPost($id)
    {
        $blogcat = BlogCategory::latest()->get();
        $post = BlogPost::find($id);
        return view('admin.backend.blogpost.edit_post', compact('post', 'blogcat'));
    }

    public function UpdateBlogPost(Request $request)
    {
        $post_id = $request->id;
        $post = BlogPost::find($post_id);

        if ($request->file('image')) {
            $image = $request->file('image');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()) . '.' .
                $image->getClientOriginalExtension();
            $img = $manager->read($image->getRealPath());
            $img->resize(746, 500)->save(public_path('upload/post/' . $name_gen));
            $save_url = 'upload/post/' . $name_gen;

            if (file_exists(public_path($post->image))) {
                @unlink(public_path($post->image));
            }

            BlogPost::find($post_id)->update([
                'blogcat_id' => $request->blogcat_id,
                'post_title' => $request->post_title,
                'post_slug' => strtolower(str_replace(' ', '-', $request->post_title)),
                'long_descp' => $request->long_descp,
                'image' => $save_url,

            ]);
            $notification = array(
                'message' => 'Blog Post Updated with Image Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('all.blog.post')->with($notification);
        } else {
            BlogPost::find($post_id)->update([
                'blogcat_id' => $request->blogcat_id,
                'post_title' => $request->post_title,
                'post_slug' => strtolower(str_replace(' ', '-', $request->post_title)),
                'long_descp' => $request->long_descp,

            ]);
            $notification = array(
                'message' => 'Blog Post Updated without Image Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('all.blog.post')->with($notification);
        }
    }

    public function DeleteBlogPost($id)
    {

        $item = BlogPost::find($id);
        $img = $item->image;
        unlink($img);
        BlogPost::find($id)->delete();

        $notification = array(
            'message' => 'Blog Post Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }



}
