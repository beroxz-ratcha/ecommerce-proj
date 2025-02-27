<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\RegisterSellerRequest;
use App\Http\Resources\UserResource;
use App\Models\Api\User;
use App\Models\Customer;
use App\Models\Seller;
use App\Enums\CustomerStatus;
use App\Enums\SellerStatus;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $sortField = request('sort_field', 'updated_at');
        $sortDirection = request('sort_direction', 'desc');

        $query = User::query()
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return UserResource::collection($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserRequest $request)
    {
        $data = $request->validated();

        if (!in_array($data['role'], [1, 2, 3])) {
            throw new \InvalidArgumentException('Invalid role specified');
        }

        $data['email_verified_at'] = now();
        $data['password'] = Hash::make($data['password']);

        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        $user = User::create($data);

        if ($data['role'] === 2 || $data['role'] === '2') {
            $seller = new Seller();
            $seller->user_id = $user->id;
            $seller->store_name = $user->name;
            $seller->save();
        }

        if ($data['role'] === 3 || $data['role'] === '3') {
            $customer = new Customer();
            $names = explode(" ", $user->name);
            $customer->user_id = $user->id;
            $customer->status = CustomerStatus::Disabled;
            $customer->first_name = $names[0];
            $customer->last_name = $names[1] ?? '';
            $customer->save();
        }

        return new UserResource($user);
    }

    public function registerSeller(RegisterSellerRequest $request)
    {
        $data = $request->validated();

        $data['role'] = 2;

        $data['email_verified_at'] = now();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        $seller = new Seller();
        $seller->user_id = $user->id;
        $seller->seller_name = $data['name'];
        $seller->store_name = $data['store_name'];
        $seller->store_phone = $data['store_phone'];
        $seller->status = SellerStatus::Active;
        $seller->save();

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User         $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        $emailValidation = [
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
        ];

        $validator = Validator::make($data, $emailValidation);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        if (isset($data['role'])) {
            if (!in_array($data['role'], [1, 2, 3])) {
                throw new \InvalidArgumentException('Invalid role specified');
            }
            $data['role'] = $data['role'];
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $data['updated_by'] = $request->user()->id;

        $user->update($data);

        return new UserResource($user);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->noContent();
    }
}
