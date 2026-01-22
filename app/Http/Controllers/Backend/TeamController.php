<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class TeamController extends Controller
{
    public function AllTeam()
    {
        $team = Team::latest()->get();
        return view('admin.backend.team.all_team', compact('team'));
    }

    public function AddTeam()
    {
        return view('admin.backend.team.add_team');
    }

    public function StoreTeam(Request $request)
    {

        if ($request->file('image')) {
            $image = $request->file('image');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()) . '.' .
                $image->getClientOriginalExtension();
            $img = $manager->read($image->getRealPath());
            $img->resize(306, 400)->save(public_path('upload/team/' . $name_gen));
            $save_url = 'upload/team/' . $name_gen;

            Team::create([
                'name' => $request->name,
                'position' => $request->position,
                'image' => $save_url,

            ]);
        }

        $notification = array(
            'message' => 'Team Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.team')->with($notification);
    }

    public function EditTeam($id)
    {
        $team = Team::find($id);
        return view('admin.backend.team.edit_team', compact('team'));
    }

    public function UpdateTeam(Request $request)
    {
        $team_id = $request->id;
        $team = Team::find($team_id);

        if ($request->file('image')) {
            $image = $request->file('image');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()) . '.' .
                $image->getClientOriginalExtension();
            $img = $manager->read($image->getRealPath());
            $img->resize(306, 400)->save(public_path('upload/team/' . $name_gen));
            $save_url = 'upload/team/' . $name_gen;

            if (file_exists(public_path($team->image))) {
                @unlink(public_path($team->image));
            }

            Team::find($team_id)->update([
                'name' => $request->name,
                'position' => $request->position,
                'image' => $save_url,

            ]);
            $notification = array(
                'message' => 'Team Updated with Image Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('all.team')->with($notification);
        } else {
            Team::find($team_id)->update([
                'name' => $request->name,
                'position' => $request->position,

            ]);
            $notification = array(
                'message' => 'Team Updated without Image Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('all.team')->with($notification);
        }
    }

    public function DeleteTeam($id)
    {

        $item = Team::find($id);
        $img = $item->image;
        unlink($img);
        Team::find($id)->delete();

        $notification = array(
            'message' => 'Team Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }
}
