<?php

namespace App\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Laravolt\Mural\CommentableTrait;
use Laravolt\Mural\Contracts\Commentable;
use Laravolt\Votee\Traits\Voteable;
use Prettus\Repository\Contracts\Presentable;
use Prettus\Repository\Traits\PresentableTrait;
use Sofa\Eloquence\Eloquence;

class Fase extends Model implements Presentable, Commentable
{
    use PresentableTrait, CommentableTrait, Voteable, Eloquence;

    protected $table = 'fase';

    protected $fillable = ['description', 'scope', 'instansi_terkait', 'start_date', 'end_date', 'progress', 'kendala', 'type', 'pic', 'target', 'pagu'];

    protected $dates = ['start_date', 'end_date'];

    protected $with = ['programKerja', 'satker'];

    public function programKerja()
    {
        return $this->belongsTo(ProgramKerja::class, 'proker_id');
    }

    public function satker()
    {
        return $this->belongsTo(Satker::class, 'satker_id');
    }

    public function scopeArsip($query)
    {
        return $query->where('end_date', '<', Carbon::now()->toDateString());
    }

    public function scopeBerjalan($query)
    {
        return $query->where('end_date', '>=', Carbon::now()->toDateString());
    }

    public function scopeBySatker($query, $id)
    {
        if ($id) {
            $query->where('satker_id', '=', $id);
        }

        return $query;
    }

    public function scopeByFase($query, $type)
    {
        if ($type) {
            $query->where('type', '=', $type);
        }

        return $query;
    }

    public function scopeByYear($query, $year)
    {
        if ($year) {
            $query->whereRaw("YEAR(start_date) = $year");
        }

        return $query;
    }

    public function getCommentableTitleAttribute()
    {
        return $this->present('name');
    }

    public function getCommentablePermalinkAttribute()
    {
        return $this->present('url');
    }
}
