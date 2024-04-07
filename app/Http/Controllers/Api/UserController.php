<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Api\UserStoreRequest;
use App\Http\Requests\Api\UserUpdateRequest;

class UserController extends Controller
{
    /*
     * User Listing api with Pagination
     */
    public function index(Request $request)
    {
        try {
            return response()->json([
                'status' => true,
                'message' => 'Successfully logged out',
                'data' => User::latest()->paginate($request->input('per_page', 5))
            ]);
        } catch (\Exception $ex) {
            return response()->json(['status' => false, 'data' => [], 'message' => $ex->getMessage()], 404);
        }
    }

    /*
     * User Store api
     */
    public function storeUser(UserStoreRequest $request)
    {
        try {
            $user = User::create(['firstname' => $request->firstname, 'lastname' => $request->lastname, 'email' => $request->email, 'mobile' => $request->mobile, 'pincode' => $request->pincode, 'gender' => $request->gender, 'address' => $request->address, 'city' => $request->city, 'state' => $request->state, 'country' => $request->country, 'birthdate' => date('Y-m-d', strtotime($request->birthdate)), 'password' => Hash::make('Password@123')]);
            return response()->json([
                'status' => true,
                'message' => "User Created Successfully...!",
                'data' => $user
            ]);
        } catch (\Exception $ex) {
            return response()->json(['status' => false, 'data' => [], 'message' => $ex->getMessage()], 404);
        }
    }

    /*
     * User detail view by ID api
     */
    public function showUser(Request $request, $id)
    {
        try {
            if ($user = User::findOrFail($id)) {
                return response()->json([
                    'status' => true,
                    'message' => "User Details Get Successfully...!",
                    'data' => $user
                ]);
            }
            throw new \Exception('User details not found...!', 403);
        } catch (\Exception $ex) {
            return response()->json(['status' => false, 'data' => [], 'message' => $ex->getMessage()], 404);
        }
    }

    /*
     * User detail update by ID api
     */
    public function updateUser(UserUpdateRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update(['firstname' => $request->firstname, 'lastname' => $request->lastname, 'email' => $request->email, 'mobile' => $request->mobile, 'pincode' => $request->pincode, 'gender' => $request->gender, 'address' => $request->address, 'city' => $request->city, 'state' => $request->state, 'country' => $request->country, 'birthdate' => date('Y-m-d', strtotime($request->birthdate))]);
            return response()->json([
                'status' => true,
                'data' => $user,
                'message' => 'User Updated Successfully...!',
            ], 201);
        } catch (\Exception $ex) {
            return response()->json(['status' => false, 'data' => [], 'message' => $ex->getMessage()], 404);
        }
    }

    /*
     * User delete by ID api
     */
    public function deleteUser(Request $request, $id)
    {
        try {
            $user = User::where('id', $id)->firstorfail()->delete();
            if ($user) {
                return response()->json([
                    'status' => true,
                    'message' => 'User Deleted Successfully...!',
                    'data' => []
                ], 201);
            }
            throw new \Exception('User details not found for delete...!', 403);
        } catch (\Exception $ex) {
            return response()->json(['status' => false, 'data' => [], 'message' => $ex->getMessage()], 404);
        }
    }

    /*
     * User detail filter by params api
     * USERFILTER
     */
    public function filterUser(Request $request)
    {
        try {
            $query = User::query();
            // Search Filters
            $query->when($request->filled('firstname'), function ($query) use ($request) {
                return $query->where('firstname', 'like', '%' . $request->firstname . '%');
            })->when($request->filled('gender'), function ($query) use ($request) {
                return $query->where('gender', $request->gender);
            })->when($request->filled('from_date') && $request->filled('to_date'), function ($query) use ($request) {
                return $query->whereDate("created_at", '>=', date('Y-m-d', strtotime($request->from_date)))
                    ->whereDate("created_at", '<=', date('Y-m-d', strtotime($request->to_date)));
            });
            $user = $query->paginate($request->input('per_page', 5));

            if ($user->isNotEmpty()) {
                return response()->json([
                    'status' => true,
                    'message' => 'User Filter Records Get Successfully...!',
                    'data' => $user,
                ], 201);
            }
            throw new \Exception('No Filter User details founds...!', 403);
        } catch (\Exception $ex) {
            return response()->json(['status' => false, 'data' => [], 'message' => $ex->getMessage()], 404);
        }
    }
}
