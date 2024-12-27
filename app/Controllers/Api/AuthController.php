<?php

namespace App\Controllers\Api;

use App\Helpers\ResponseHelper;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Shield\Models\UserModel;

class AuthController extends ApiController
{   
    function __construct()
    {
        parent::__construct();
    }

    /**
     * user register endpoint
     * 
     */
    public function register()
    {
        $validation = \Config\Services::validation();
		$validationRules = $validation->getRuleGroup('register');

        if(!$this->validate($validationRules))
        {
            $response = ResponseHelper::getResponse(
                ResponseInterface::HTTP_BAD_REQUEST,
                $this->validator->getErrors(),
                true,
            );

            return $this->respond($response, ResponseInterface::HTTP_BAD_REQUEST);
        }

        $userModel = new UserModel();
        $user = new User([
            'username' => $this->request->getVar('username'),
		    'email' => $this->request->getVar('email'),
		    'password' => $this->request->getVar('password')
        ]);

        $userModel->save($user);

        $response = ResponseHelper::getResponse(
            ResponseInterface::HTTP_OK,
            'user created successfully!',
            false,
        );

        return $this->respond($response, ResponseInterface::HTTP_OK);
    }
    
    /**
     * user login endpoint
     * generate API token
     * 
     */
    public function login()
    {

        if(auth()->loggedIn())
        {
            auth()->logout();
        }

        $validation = \Config\Services::validation();
		$validationRules = $validation->getRuleGroup('login');

        if(!$this->validate($validationRules))
        {
            $response = ResponseHelper::getResponse(
                ResponseInterface::HTTP_BAD_REQUEST,
                $this->validator->getErrors(),
                true,
            );

            return $this->respond($response, ResponseInterface::HTTP_BAD_REQUEST);
        }

        $credentials = [
		    'email' => $this->request->getVar('email'),
		    'password' => $this->request->getVar('password')
        ];

        $loginAttempt = auth()->attempt($credentials);

        if(!$loginAttempt->isOK())
        {
            $response = ResponseHelper::getResponse(
                ResponseInterface::HTTP_BAD_REQUEST,
                'Invalid credentials',
                true,
            );

            return $this->respond($response, ResponseInterface::HTTP_BAD_REQUEST);
        }

        $userModel = new UserModel();
        $userData = $userModel->findById(auth()->id());
        $token = $userData->generateAccessToken($userData->username);
        $token = $token->raw_token;

        $response = ResponseHelper::getResponse(
            ResponseInterface::HTTP_OK,
            'user logged!',
            false, [
                'token' => $token
            ]
        );

        return $this->respond($response, ResponseInterface::HTTP_OK);
    }

    public function userProfile()
    {
        if(auth('tokens')->loggedIn())
        {
            $userModel = new UserModel;
            $user = $userModel->findById(auth()->id());

            $response = ResponseHelper::getResponse(
                ResponseInterface::HTTP_OK,
                'user profile',
                false, [
                    'user' => $user
                ]
            );
    
            return $this->respond($response, ResponseInterface::HTTP_OK);
        }
    }

    public function invalidRequest()
    {
        $response = ResponseHelper::getResponse(
            ResponseInterface::HTTP_BAD_REQUEST,
            'Invalid request, please login',
            true,
        );

        return $this->respond($response, ResponseInterface::HTTP_BAD_REQUEST);
    }
    
    public function logout()
    {
        if(auth('tokens')->loggedIn())
        {
            auth()->logout();
            auth()->user()->revokeAllAccessTokens();
    
            $response = ResponseHelper::getResponse(
                ResponseInterface::HTTP_OK,
                'user logout successfully',
                false, 
                []
            );
    
            return $this->respond($response, ResponseInterface::HTTP_OK);
        }
    }
}
