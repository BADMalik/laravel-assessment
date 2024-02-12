<?php

namespace DTApi\Http\Controllers;

use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;
use DTApi\Http\Services\UserService;
use DTApi\Http\Services\BooksService;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{

    /**
     * @var BookingRepository
     */
    protected $repository;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var BooksService
     */
    protected $booksService;

    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */
    public function __construct(BookingRepository $bookingRepository, UserService $userService, BooksService $booksService)
    {
        $this->booksService = $booksService;
        $this->repository = $bookingRepository;
        $this->userService = $userService;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        try {
            return response($this->userService->getUserDetails($request));
        } catch (\Exception $e) {
            Log::error($e);
            return $e->getMessage();
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        try {
            return response($this->userService->getUserJobInfo($id));
        } catch (\Exception $e) {
            Log::error($e);
            return $e->getMessage();
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $response = $this->userService->storeUserInfo($request->__authenticatedUser, $data);
            return response($response);
        } catch (\Exception $e) {
            Log::error($e);
            return $e->getMessage();
        }
    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
        try {
            $data = $request->all();
            $cuser = $request->__authenticatedUser;
            return response($this->userService->updateUserJob($id, $cuser, array_except($data, ['_token', 'submit'])));
        } catch (\Exception $e) {
            Log::error($e);
            return $e->getMessage();
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function immediateJobEmail(Request $request)
    {
        try {
            return response($this->userService->immediateJobEmail($request->all()));
        } catch (\Exception $e) {
            Log::error($e);
            return $e->getMessage();
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getHistory(Request $request)
    {
        try {
            $response = $this->userService->getHistory($request->get('user_id'), $request);
            return response($response);
        } catch (\Exception $e) {
            Log::error($e);
            return $e->getMessage();
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function acceptJob(Request $request)
    {
        try {
            return  response($this->userService->acceptJob($$request->all(), $request->__authenticatedUser));
        } catch (\Exception $e) {
            Log::error($e);
            return $e->getMessage();
        }
    }

    public function acceptJobWithId(Request $request)
    {
        try {
            return response($this->userService->acceptJobWithId($request->get('job_id'), $request->__authenticatedUser));
        } catch (\Exception $e) {
            Log::error($e);
            return $e->getMessage();
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function cancelJob(Request $request)
    {
        try {
            return response($this->userService->cancelJob($request->all(), $request->__authenticatedUser));
        } catch (\Exception $e) {
            Log::error($e);
            return $e->getMessage();
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function endJob(Request $request)
    {
        try {
            return response($this->userService->endJob($request->all()));
        } catch (\Exception $e) {
            Log::error($e);
            return $e->getMessage();
        }
    }

    public function customerNotCall(Request $request)
    {
        try {
            return response($this->userService->customerNotCall($request->all()));
        } catch (\Exception $e) {
            Log::error($e);
            return $e->getMessage();
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPotentialJobs(Request $request)
    {
        try {
            return response($this->userService->getPotentialJobs($request->__authenticatedUser));
        } catch (\Exception $e) {
            Log::error($e);
            return $e->getMessage();
        }
    }

    public function distanceFeed(Request $request)
    {
        try {
            $this->userService->distanceFeed($request->all());
            return response('Record updated!');
        } catch (\Exception $e) {
            Log::error($e);
            return $e->getMessage();
        }
    }

    public function reopen(Request $request)
    {
        try {
            return response($this->userService->reopen($request->all()));
        } catch (\Exception $e) {
            Log::error($e);
            return $e->getMessage();
        }
    }

    public function resendNotifications(Request $request)
    {
        try {
            $this->userService->resendNotifications($request->all());
            return response(['success' => 'Push sent']);
        } catch (\Exception $e) {
            Log::error($e);
            return $e->getMessage();
        }
    }

    /**
     * Sends SMS to Translator
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resendSMSNotifications(Request $request)
    {
        try {
            $this->userService->sendSMSNotificationToTranslator($request->all());
            return response(['success' => 'SMS sent']);
        } catch (\Exception $e) {
            Log::error($e);
            return $e->getMessage();
        }
    }
}
