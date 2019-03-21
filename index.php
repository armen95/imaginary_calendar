<?php 

  class Calendar{

    const START_YEAR = 1990;
    const COUNT_MONTH = 13;
    const START_MONTH = 1;
    const START_DAY = 1;
    const WEEK_DAYS = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

    private $year = '';
    private $month = '';
    private $day = '';
    private $fullDate = '';
    private $week = '';
    private $separator = '-';

    public function __construct( $date = null ){

       $date ? $this->setDate( $date ) : $this->installDate(); 
    
    }

    public function getDay(){
      return $this->day;
    }

    public function getMonth(){
      return $this->month;
    }

    public function getYear(){
      return $this->year;
    }

    public function getWeek(){
      return $this->week;
    }

    public function getFullDate(){
      return $this->fullDate;
    }


    public function isValidDate( string $date ){

      if (preg_match ("/^([0-9]{4})[-\/ ]?([0-9]{0,2})[-\/ ]?([0-9]{0,2})$/", $date, $split))
      {
        preg_match('/[^\d]/',$date,$separator);
        if(!empty($separator)) $this->separator = $separator[0];

        $year = $split[1];
        $month = (int)$split[2] ? ((int)$split[2] < 10 ? '0'.(int)$split[2] : (int)$split[2] ): '01' ;
        $day = (int)$split[3] ? ((int)$split[3] < 10 ? '0'.(int)$split[3] : (int)$split[3] )  : '01' ;

         if( $this->isValidYear( $year ) &&  $this->isValidMonth( $month ) &&  $this->isValidDay( $year , $month , $day ) )
         {
            $this->year = $year;
            $this->month = $month;
            $this->day = $day;
            $this->createWeek();
            $this->fullDate = $year.$this->separator.$month.$this->separator.$day." ".$this->week;
            return true;
         }
         return;
      }

      echo "<b><i>Date error:</i></b> <br> The date format is wrong. Got $date";
      return;
    }

    public function isValidYear( $year ){
      $year = (int)$year;
      if($year >= self::START_YEAR)
        return $year;
      echo "<b><i>Year error:</i></b> <br> Year should be at least 1990. Got $year";
      return;
    }

    public function isValidMonth( $month ){
      $month = (int)$month;
      if($month <= self::COUNT_MONTH && $month >= self::START_MONTH)
        return $month;
      echo "<b><i>Month error:</i></b> <br> Month must be 13 max. Got $month";
      return;
    }

    public function isValidDay( $year , $month , $day ){
      $year = (int)$year;
      $month = (int)$month;
      $day = (int)$day;

      if($this->isLeapYear($year) && $month == 13 && $day == 21 )
      {
        echo "<b><i>Date error:</i></b> <br> The 13th month of Leap Year don't have the 21st day. Got $year/$month/$day";
        return;
      }
      if($month % 2 == 1)
      {
        if($day <= 21 && $day >= self::START_DAY)
          return $day;
        echo "<b><i>Date error:</i></b> <br> The odd month have a max 21 days ( if year is not leap and the month is not 13th ). Got month $month and the date $year/$month/$day";
      }
      else{
        if($day <= 22 && $day >= self::START_DAY)
          return $day;
        echo "<b><i>Date error:</i></b> <br> The even month have a max 22 days. Got month $month and the date $year/$month/$day";
      }
      return;
    }

    public function isLeapYear( $year ){
      return $year % 5 == 0;
    }

    private function getCountsLeapYear(){
      $later = $this->year - self::START_YEAR;
      if( $later % 5 == 0 ){
        $count = $later / 5 ;
      }else{
        $count = floor($later/5) + 1;
      }
      return $count;
    }

    private function installDate(){
      
      $currentDate = date('Y-m-d');
      $startDate = self::START_YEAR.'-'.self::START_MONTH.'-'.self::START_DAY;
      $fromDate = date_create( $startDate );
      $toDate = date_create( $currentDate );
      $interval = date_diff( $fromDate , $toDate ) ->format('%a');

      $year = 1990;
      while ( $interval >= 278 ) {
        if( $interval == 278 && !$this->isLeapYear( $year + 1 ) ) break;
        if( $year % 5 == 0 ) $interval -= 278;
        else $interval -= 279;
        $year += 1;
      }

      $month = 1;
      while ( $interval >= 21 ) {
        if( $month % 2 == 1 ) $interval -= 21;
        else $interval -= 22;
        $month += 1; 
      }

      $day = ( $interval == 0) ? 1 : $interval;

      $this->year = $year;
      $this->month = ( $month < 10) ? "0".$month : $month;
      $this->day = ( $day < 10) ? "0".$day : $day;
      $this->createWeek();
      $this->fullDate = $this->year.$this->separator.$this->month.$this->separator.$this->day." ".$this->week;

    }

    private function setDate( $date ){
      $this->isValidDate( $date );
    }

    private function createWeek(){

      $days_interval = 0;
      $days_interval += ((int)$this->year - self::START_YEAR)*279 - $this->getCountsLeapYear();

      $month = 1;
      while ( $month < (int)$this->month ) {
        if( $month % 2 == 1 ) $days_interval += 21;
        else $days_interval += 22;
        $month += 1;
      }

      $days_interval += (int)$this->day;
      $week_index = $days_interval % 7 ;
      $this->week = self::WEEK_DAYS[$week_index];

    }

  }


echo "Date format: <b>YYYY/MM/DD</b> or <b>YYYY-MM-DD</b> or <b>YYYY MM DD</b>";
echo "<br><br>";

$d = new Calendar();
echo $d->getFullDate() ."<br>";

$d = new Calendar('1990 11 01');
echo "<br>";
echo $d->getFullDate() ."<br>";

$d = new Calendar('1991-01-01');
echo "<br>";
echo $d->getFullDate() ."<br>";

$d = new Calendar('1995/11/15');
echo "<br>";
echo $d->getFullDate() ."<br>";

$d = new Calendar('1993/05/21');
echo "<br>";
echo $d->getFullDate() ."<br>";

$d = new Calendar('2010/11/01');
echo "<br>";
echo $d->getFullDate() ."<br>";

$d = new Calendar('2008/13/14');
echo "<br>";
echo $d->getFullDate() ."<br>";

$d = new Calendar('2008/1/1');
echo "<br>";
echo $d->getFullDate() ."<br>";

$d = new Calendar('2026/13');
echo "<br>";
echo $d->getFullDate() ."<br>";

$d = new Calendar('2017');
echo "<br>";
echo $d->getFullDate() ."<br>";

echo "<br>";
echo "<b><i>Validation and error messages when date format is wrong !</i></b>";
echo "<br>";


$d = new Calendar('2');
echo "<br>";
echo $d->getFullDate() ."<br>";

$d = new Calendar('2008/11/22');
echo "<br>";
echo $d->getFullDate() ."<br>";

$d = new Calendar('2009/06/23');
echo "<br>";
echo $d->getFullDate() ."<br>";

$d = new Calendar('1990/13/21');
echo "<br>";
echo $d->getFullDate() ."<br>";

$d = new Calendar('2005/13/21');
echo "<br>";
echo $d->getFullDate() ."<br>";

$d = new Calendar('2018/14/14');
echo "<br>";
echo $d->getFullDate() ."<br>";

$d = new Calendar('1988/11/14');
echo "<br>";
echo $d->getFullDate() ."<br>";
