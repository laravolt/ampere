<?php

namespace App\Repositories;

use App\Enum\FaseType;
use App\Presenters\FasePresenter;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Entities\Fase;

/**
 * Class FaseRepositoryEloquent
 * @package namespace App\Repositories;
 */
class FaseRepositoryEloquent extends BaseRepository implements FaseRepository
{

    protected $skipPresenter = true;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Fase::class;
    }

    public function presenter()
    {
        return FasePresenter::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function lists()
    {
        return collect(FaseType::toArray())->prepend('-- Semua Fase --');
    }

    public function terbaru($limit)
    {
        $results = $this->model->latest()->limit($limit)->get();

        return $this->parserResult($results);
    }

    public function terpopuler($limit)
    {
        $results = $this->model->mostVoted()->limit($limit)->get();

        return $this->parserResult($results);
    }
}
