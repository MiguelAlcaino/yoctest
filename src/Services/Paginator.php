<?php
/**
 * Created by PhpStorm.
 * User: malcaino
 * Date: 22/07/18
 * Time: 17:34
 */

namespace App\Services;


class Paginator
{
    /**
     * @var int
     */
    private $count;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $currentPage;

    /**
     * Paginator constructor.
     * @param int $count
     * @param int $limit
     * @param int $currentPage
     */
    public function __construct(int $count, int $limit, int $currentPage)
    {
        $this->count = $count;
        $this->limit = $limit;
        $this->currentPage = $currentPage;
    }


    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return Paginator
     */
    public function setCount(int $count): Paginator
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @return Paginator
     */
    public function setLimit(int $limit): Paginator
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @param int $currentPage
     * @return Paginator
     */
    public function setCurrentPage(int $currentPage): Paginator
    {
        $this->currentPage = $currentPage;
        return $this;
    }

    public function hasNextPage(){
        $calc = (($this->currentPage-1)*$this->limit) + $this->limit;
        return $calc < $this->count;
    }

    public function hasPreviousPage(){
        return $this->currentPage > 1;
    }
}