<?php

namespace DTApi\Http\Services;


use DTApi\Repository\BookingRepository;


/**
 * Class UserServiceController
 */
class BooksService
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
}
