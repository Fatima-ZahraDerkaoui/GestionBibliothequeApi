<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller; 
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use App\Http\Models\Identity\User;
use App\Http\Resources\UserResource ;
use App\Http\Resources\UserCollection ;
use App\Http\Resources\LoginRessource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    { 
        //$currentUser= $request->user();  
        //if($currentUser->isAdmin())
           return  User::all(); 

       return new UserResource($currentUser);
    }

    public function show(User $data)
    { 
        return new UserResource($data); 
    }

    public function store(Request $request)
    {
        $isActivated=$request->input('activate');

        $data=User::create($request->all() + ['activated_at' => $isActivated?Carbon::now():null]);
 
        if($isActivated){  
            $data->activated_at== Carbon::now(); 
        } 

        return response()->json($data, 201);
    }

    public function updateA(Request $request, User $data)
    {
        
        $isActivated=$request->input('activate');  
        $data->update($request->all()+ ['activated_at' => $isActivated?Carbon::now():null]);
    
        if($isActivated){ 
            $data->markEmailAsVerified(); 
        }   
        
        return response()->json($data, 200);
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
         $user = User::find($id);
 
         if (!$user) {
             return response()->json(['message' => 'User not found'], 404);
         }
 
         $data = $request->all();
 
         // if (isset($data['password'])) {
         //     $data['password'] = Hash::make($data['password']);
         // }
 
         $user->update($data);
 
         return response()->json(new UserResource($user), 200);
     }

    public function delete(User $data)
    {
        $data->delete();

        return response()->json(null, 204);
    }
    
   

    
    
    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required', 
            'password_confirmation' => 'required|min:6',
            'password' => 'required|min:6|confirmed',
         ]);

        $user = $request->user();  
        $hashedPassword = $user->password;  
        if (Hash::check($request->old_password, $hashedPassword)) {
                
        $user->fill(['password' => $request->password])->save(); 
                 return $this->respondWithMessage('Your password has been changed.',$user); 
        }   
        return response()->json(['error' => 'Your password has not been changed.'], 401); 
    }

    public function forgot_password(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'email' => "required|email",
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $arr = array("status" => 400, "message" => $validator->errors()->first(), "data" => array());
        } else {
            try {
                $response = Password::sendResetLink($request->only('email'), function (Message $message) {
                    $message->subject($this->getEmailSubject());
                });
                switch ($response) {
                    case Password::RESET_LINK_SENT:
                        return \Response::json(array("status" => 200, "message" => trans($response), "data" => array()));
                    case Password::INVALID_USER:
                        return \Response::json(array("status" => 400, "message" => trans($response), "data" => array()));
                }
            } catch (\Swift_TransportException $ex) {
                $arr = array("status" => 400, "message" => $ex->getMessage(), "data" => []);
            } catch (Exception $ex) {
                $arr = array("status" => 400, "message" => $ex->getMessage(), "data" => []);
            }
        }
        return \Response::json($arr);
    }

    public function change_password(Request $request)
    {
        $input = $request->all();
        $userid = Auth::guard('api')->user()->id;
        $rules = array(
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $arr = array("status" => 400, "message" => $validator->errors()->first(), "data" => array());
        } else {
            try {
                if ((Hash::check(request('old_password'), Auth::user()->password)) == false) {
                    $arr = array("status" => 400, "message" => "Check your old password.", "data" => array());
                } else if ((Hash::check(request('new_password'), Auth::user()->password)) == true) {
                    $arr = array("status" => 400, "message" => "Please enter a password which is not similar then current password.", "data" => array());
                } else {
                    User::where('id', $userid)->update(['password' => Hash::make($input['new_password'])]);
                    $arr = array("status" => 200, "message" => "Password updated successfully.", "data" => array());
                }
            } catch (\Exception $ex) {
                if (isset($ex->errorInfo[2])) {
                    $msg = $ex->errorInfo[2];
                } else {
                    $msg = $ex->getMessage();
                }
                $arr = array("status" => 400, "message" => $msg, "data" => array());
            }
        }
        return \Response::json($arr);
    }


     /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        $resource = User::find($id);
    
        if (!$resource) {
            return response()->json(['message' => 'Resource not found'], 404);
        }
    
        $resource->delete();
    
        return response()->json($resource, 200);
    }  
}
