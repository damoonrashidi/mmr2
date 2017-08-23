<?php
/**
 * User - Description
 * extended description
 **/
class User extends Model {

    //database table for this model
    static $table = "user";
    static $validate = [
        'points' => 'Validate::positive',
    ];

    public $id = null;
    public $created_at = null;
    public $modified_at = null;
    public $username = null;
    public $points = null;

    public function __construct($data = []) {

        foreach ($data as $k => $v) {
            $this->$k = $v;
        }

        if ($this->points == null) {
            $this->points = 1200;
        }

        return $this;
    }

    public function history() {
        $history = SinglesHistory::where(['winner' => $this->id, 'loser' => $this->id], "OR");
        return $history === null ? new Resource([]) : $history;
    }

    public function wins() {
        $wins = SinglesHistory::where(['winner' => $this->id]);
        return $wins == null ? [] : $wins->to_array();
    }

    public function losses() {
        $losses = SinglesHistory::where(['loser' => $this->id]);
        return $losses == null ? [] : $losses->to_array();
    }

    public function teams() {
        return Team::where(['captain' => $this->id, 'mate' => $this->id], "OR");
    }

    public function expected(User $opponent) {
        return 1 / (1 + pow(10, ($opponent->points - $this->points) / 400));
    }

    public function bounty() {
        $wins = $this->consecutiveWins();
        return $wins < 3 ? 0 : $wins * 1.5;
    }

    public function adjustMMR(User $opponent, bool $win) {
        $adjustment = $win ? 1 : 0;
        $diff = $this->points - $opponent->points;
        $adjusted = $this->points + MMR::$K * ($adjustment - $this->expected($opponent)) + $this->bounty() + $opponent->bounty();
        return round($adjusted);
    }

    public function consecutiveWins() {
        $games = SinglesHistory::where(['winner' => $this->id, 'loser' => $this->id], "OR");
        if ($games === null) {
            return 0;
        }
        $consecutive = 0;
        $stop = false;
        $games->reverse()->each(function ($game) use (&$consecutive, &$stop) {
            if ($game->winner != $this->id || $stop) {
                $stop = true;
                return;
            } else {
                $consecutive++;
            }
        });
        return $consecutive;
    }

}
