<?php

namespace DTApi\Http\Services;

use Illuminate\Http\Request;
use DTApi\Models\Job;
use DTApi\Models\Distance;
use DTApi\Repository\BookingRepository;
use Exception;
use LogicException;

/**
 * Class UserServiceController
 */
class UserService
{
    /**
     * @var BookingRepository
     */
    protected $repository;

    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */
    public function __construct(BookingRepository $bookingRepository)
    {
        $this->repository = $bookingRepository;
    }

    public function getUserDetails($data)
    {
        try {
            $user_id = $data->get('user_id');
            $response = [];

            //transaction start
            if ($user_id) {
                return $this->repository->getUsersJobs($user_id);
            }

            if ($data->__authenticatedUser->user_type == config('admin_role_id') || $data->__authenticatedUser->user_type == env('super_admin_role_id')) {
                return $this->repository->getAll($data);
            }
            //transaction end 
            return $response;
        } catch (\Exception $e) {
            //rollback transaction    
            Log::error($e);
            throw new Exception($e);
        }
    }

    public function getUserJobInfo($id)
    {
        try {
            $response = [];
            //transaction start
            $response = $this->repository->with('translatorJobRel.user')->find($id);
            //transaction end
            return $response;
        } catch (\Exception $e) {
            //rollback transaction    
            Log::error($e);
            throw new Exception($e);
        }
    }

    public function storeUserInfo($user, $data)
    {
        try {

            //transaction start
            return $this->repository->store($user, $data);
            //transaction end 
        } catch (\Exception $e) {
            //rollback transaction    
            Log::error($e);
            throw new Exception($e);
        }
    }

    public function updateUserJob($id, $cuser, $data)
    {
        try {
            //transaction start
            return $this->repository->updateJob($id, array_except($data, ['_token', 'submit']), $cuser);
            //transaction end 
        } catch (\Exception $e) {
            //rollback transaction    
            Log::error($e);
            throw new Exception($e);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function immediateJobEmail($data)
    {
        try {

            //transaction start
            return $this->repository->storeJobEmail($data);
            //transaction end 
        } catch (\Exception $e) {
            //rollback transaction    
            Log::error($e);
            throw new Exception($e);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getHistory($user, $data)
    {
        try {
            $response = null;

            //transaction start
            if (!empty($user)) {
                $response = $this->repository->getUsersJobsHistory($user, $data);
            }
            //transaction end 
            return $response;
        } catch (\Exception $e) {
            //rollback transaction    
            Log::error($e);
            throw new Exception($e);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function acceptJob($data, $user)
    {
        try {

            //transaction start
            return $this->repository->acceptJob($data, $user);
            //transaction end 
        } catch (\Exception $e) {
            //rollback transaction    
            Log::error($e);
            throw new Exception($e);
        }
    }

    public function acceptJobWithId($data, $user)
    {
        try {
            //transaction start
            return $this->repository->acceptJobWithId($data, $user);
            //transaction end 
        } catch (\Exception $e) {
            //rollback transaction    
            Log::error($e);
            throw new Exception($e);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function cancelJob($data, $user)
    {
        try {

            //transaction start
            return $this->repository->cancelJobAjax($data, $user);
            //transaction end 
        } catch (\Exception $e) {
            //rollback transaction    
            Log::error($e);
            throw new Exception($e);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function endJob($data)
    {
        try {

            //transaction start
            return $this->repository->endJob($data);
            //transaction end 
        } catch (\Exception $e) {
            //rollback transaction    
            Log::error($e);
            throw new Exception($e);
        }
    }
    public function customerNotCall($data)
    {
        try {

            //transaction start
            return  $this->repository->customerNotCall($data);
            //transaction end 
        } catch (\Exception $e) {
            //rollback transaction    
            Log::error($e);
            throw new Exception($e);
        }
    }
    /**
     * @param Request $request
     * @return mixed
     */
    public function getPotentialJobs($user)
    {
        try {

            //transaction start
            return $this->repository->getPotentialJobs($user);
            //transaction end 
        } catch (\Exception $e) {
            //rollback transaction    
            Log::error($e);
            throw new Exception($e);
        }
    }

    public function distanceFeed($data)
    {
        try {

            $distance = $this->getDefault($data, 'distance');
            $time = $this->getDefault($data, 'time');
            $jobid = $this->getDefault($data, 'jobid');
            $session = $this->getDefault($data, 'session_time');
            $flagged = $this->getFlaggedValue($data);
            $manually_handled = $this->getBooleanValue($data, 'manually_handled');
            $by_admin = $this->getBooleanValue($data, 'by_admin');
            $admincomment = $this->getDefault($data, 'admincomment');

            //transaction start
            if ($time || $distance) {
                $this->updateDistance($jobid, $distance, $time);
            }

            if ($admincomment || $session || $flagged || $manually_handled || $by_admin) {
                $this->updateJob($jobid, $admincomment, $session, $flagged, $manually_handled, $by_admin);
            }
            //transaction end 
        } catch (\Exception $e) {
            //rollback transaction    
            Log::error($e);
            throw new Exception($e);
        }
    }

    private function getDefault($data, $key)
    {
        try {
            return isset($data[$key]) ? $data[$key] : '';
        } catch (\Exception $e) {
            Log::error($e);
            throw new Exception($e);
        }
    }

    private function getFlaggedValue($data)
    {
        try {
            return $data['flagged'] == 'true' && !empty($data['admincomment']) ? 'yes' : 'no';
        } catch (\Exception $e) {
            Log::error($e);
            throw new Exception($e);
        }
    }

    private function getBooleanValue($data, $key)
    {
        try {
            return $data[$key] == 'true' ? 'yes' : 'no';
        } catch (\Exception $e) {
            Log::error($e);
            throw new Exception($e);
        }
    }

    private function updateDistance($jobid, $distance, $time)
    {
        try {
            //transaction start
            Distance::where('job_id', $jobid)->update(['distance' => $distance, 'time' => $time]);
            //transaction end        
        } catch (\Exception $e) {
            //rollback transaction        
            Log::error($e);
            throw new Exception($e);
        }
    }

    private function updateJob($jobid, $admincomment, $session, $flagged, $manually_handled, $by_admin)
    {
        try {
            //transaction start
            Job::where('id', $jobid)->update([
                'admin_comments' => $admincomment,
                'flagged' => $flagged,
                'session_time' => $session,
                'manually_handled' => $manually_handled,
                'by_admin' => $by_admin
            ]);
            //transaction end   
        } catch (\Exception $e) {
            //rollback transaction    
            Log::error($e);
            throw new Exception($e);
        }
    }

    public function reopen($data)
    {

        try {
            $response = null;
            // start transaction        
            $response = $this->repository->reopen($data);
            //transaction end   
            return $response;
        } catch (\Exception $e) {
            //rollback transaction    
            Log::error($e);
            throw new Exception($e);
        }
    }
    public function resendNotifications($data)
    {
        try {
            //transaction start
            $job = $this->repository->find($data['jobid']);
            $job_data = $this->repository->jobToData($job);
            $this->repository->sendNotificationTranslator($job, $job_data, '*');
            // transaction end        
        } catch (\Exception $e) {
            //rollback transaction
            Log::error($e);
            return $e->getMessage();
        }
    }
    /**
     * @param Request $request
     */
    public function sendSMSNotificationToTranslator($data)
    {
        try {
            // transaction start
            $job = $this->repository->find($data['jobid']);
            $this->repository->jobToData($job);

            $this->repository->sendSMSNotificationToTranslator($job);
            //transaction end        
        } catch (\Exception $e) {
            //rollback transaction
            Log::error($e);
            return $e->getMessage();
        }
    }
}
