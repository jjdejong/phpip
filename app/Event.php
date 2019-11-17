<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'event';
    protected $hidden = ['creator', 'created_at', 'updated_at', 'updater'];
    protected $guarded = ['id', 'creator', 'created_at', 'updated_at', 'updater'];
    protected $touches = ['matter'];
    protected $dates = [
      'event_date'
    ];

    // use \Venturecraft\Revisionable\RevisionableTrait;
    // protected $revisionEnabled = true;
    // protected $revisionCreationsEnabled = true;
    // protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    // protected $historyLimit = 500; //Maintain a maximum of 500 changes at any point of time, while cleaning up old revisions.

    public function info() {
        return $this->hasOne('App\EventName', 'code', 'code');
    }

    public function matter() {
        return $this->belongsTo('App\Matter');
    }

    public function altMatter() {
        return $this->belongsTo('App\Matter', 'alt_matter_id');
    }

    public function link() {
        return $this->hasOne('App\Event', 'matter_id', 'alt_matter_id')->where('code', 'FIL');
    }

    public function retroLink() {
        return $this->belongsTo('App\Event', 'matter_id', 'alt_matter_id');
    }

    public function tasks() {
        /* \Event::listen('Illuminate\Database\Events\QueryExecuted', function($query) {
          var_dump($query->sql);
          var_dump($query->bindings);
          }); */
        return $this->hasMany('App\Task', 'trigger_id')->orderBy('due_date');
    }

// 	Produces a link to official published information

    public function publicUrl() {
        if (!in_array($this->code, ['FIL', 'PUB', 'GRT'])) {
            return false;
        }
        if ($this->matter->origin == 'EP') {
            $CC = 'EP';
        } else {
            $CC = $this->matter->country;
        }
        $country_code = $this->matter->country;
        $category = $this->matter->category_code;
        $removethese = ["/^$country_code/", '/ /', '/,/', '/-/', '/\//', '/\.[0-9]/'];
        $cleanednumber = preg_replace($removethese, '', $this->detail);
        $href = '';
        $pubno = '';
        if ($this->code == 'PUB' || $this->code == 'GRT') {
            // Fix US pub number for Espacenet by keeping the last 6 digits after the year
            if ($CC == 'US' && $this->code == 'PUB') {
                $cleanednumber = substr($cleanednumber, 0, 4) . substr($cleanednumber, - 6);
            }
            $href = 'http://worldwide.espacenet.com/publicationDetails/biblio?DB=EPODOC&CC=' . $CC . '&NR=' . $cleanednumber;
        } else if ($this->code == 'FIL') {
            if (defined($this->matter->publication)) {
                $pubno = preg_replace($removethese, '', $this->matter->publication->detail);
            }
            switch ($country_code) {
                case 'EP':
                    $href = 'https://register.epo.org/espacenet/application?number=EP' . $cleanednumber;
                    break;
                case 'FR':
                    if ($category == 'PAT' && $pubno) {
                        $href = 'http://bases-brevets.inpi.fr/fr/document/' . $CC . $pubno . '.html';
                    }
                    /* else if ( $category == 'TM' ) {
                      if (substr ($this->event_date, 0, 4 ) >= '2000')
                      $cleanednumber = substr ( $cleanednumber, -7 );
                      $href = 'http://bases-marques.inpi.fr/Typo3_INPI_Marques/marques_resultats_liste.html?baseFr=on&baseCommu=on&baseInter=on&rechercher=Rechercher&recherche=recherche&numero=' . $cleanednumber;
                      } */
                    break;
                case 'US':
                    $year = substr($this->event_date, 0, 4);
                    $href = 'https://register.epo.org/ipfwretrieve?apn=US.' . $year . $cleanednumber . '.A';
                    break;
                case 'GB':
                    $href = 'http://www.ipo.gov.uk/p-ipsum/Case/ApplicationNumber/' . $CC . $cleanednumber;
                    break;
                case 'EM':
                    $href = 'https://euipo.europa.eu/eSearch/#details/trademarks/' . $cleanednumber;
                    break;
            }
        }
        return $href;
    }

}
