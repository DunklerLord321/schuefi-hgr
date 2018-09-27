<?php
class paar {
	public $schueler = schueler::class;
	public $lehrer = lehrer::class;
	public $raum;
	public $tag;
	public $anfang;
	public $ende;
	public $fid;
	public $rid;
	public $paarid;
	public $erstellungstag;
	public $lehrer_dokument;
	public $schueler_dokument;
	function __construct(int $paar_id, int $lid = -1, int $sid = -1) {
		if ($lid == -1 && $sid == -1) {
			$return = query_db("SELECT unterricht.*, raum.nummer, raum.stunde FROM `unterricht` LEFT JOIN raum on raum.id = unterricht.rid WHERE unterricht.id = :id", $paar_id);
			if ($return) {
				$return = $return->fetch();
				if ($return) {
					if (!class_exists("lehrer")) {
						require 'includes/class_lehrer.php';
					}
					if (!class_exists("schueler")) {
						require 'includes/class_schueler.php';
					}
					$this->paarid = $paar_id;
					$this->schueler = new schueler(-1, $return['sid']);
					$this->lehrer = new lehrer(-1, $return['lid']);
					if(isset($return['nummer']) && $return['nummer'] != '0') {
						$this->raum = $return['nummer'];
						$this->rid = $return['rid'];
					}else{
						$this->raum = $return['treff_raum'];
					}
					$this->fid = $return['fid'];
					$this->anfang = date("H:i", strtotime($return['treff_zeit']));
					$this->ende = date("H:i", strtotime($return['treff_zeit_ende']));
					$this->erstellungstag = $return['erstellungs_date'];
					$this->tag = $return['tag'];
					$this->lehrer_dokument = $return['lehrer_dokument'];
					$this->schueler_dokument = $return['schueler_dokument'];
				}else {
					echo "Es existiert kein solches Paar";
				}
			}else {
				echo "Fehler";
			}
		}else {
			$return = query_db("SELECT unterricht.*, raum.nummer, raum.stunde FROM `unterricht` LEFT JOIN raum on raum.id = unterricht.rid WHERE lid = :lid AND sid = :sid", $lid, $sid);
			if ($return) {
				$return = $return->fetch();
				if ($return) {
					require 'includes/class_lehrer.php';
					require 'includes/class_schueler.php';
					$this->paarid = $return['id'];
					$this->schueler = new schueler(-1, $return['sid']);
					$this->lehrer = new lehrer(-1, $return['lid']);
					if(isset($return['nummer']) && $return['nummer'] != '0') {
						$this->raum = $return['nummer'];
						$this->rid = $return['rid'];
					}else{
						$this->raum = $return['treff_raum'];
					}
					$this->fid = $return['fid'];
					$this->anfang = $return['treff_zeit'];
					$this->ende = $return['treff_zeit_ende'];
					$this->erstellungstag = $return['erstellungstag'];
					$this->tag = $return['tag'];
					$this->lehrer_dokument = $return['lehrer_dokument'];
					$this->schueler_dokument = $return['schueler_dokument'];
				}else {
					echo "Es existiert kein solches Paar";
				}
			}else {
				echo "Fehler";
			}
		}
	}
	function adddokument(string $lehrer_dokument = "", string $schueler_dokument = "") {
		if (strlen($lehrer_dokument) > 0) {
			$this->lehrer_dokument = $lehrer_dokument;
			query_db("UPDATE `unterricht` SET `lehrer_dokument` = :lehrer_dokument WHERE id = :id;", $lehrer_dokument, $this->paarid);
		}
		if (strlen($schueler_dokument) > 0) {
			$this->lehrer_dokument = $schueler_dokument;
			query_db("UPDATE `unterricht` SET `schueler_dokument` = :schueler_dokument WHERE id = :id;", $schueler_dokument, $this->paarid);
		}
	}
	
	function delete() {
		$index = -1;
		for ($i=0; $i < count($this->schueler->faecher); $i++) {
			if(isset($this->schueler->faecher[$i]['fid']) && $this->schueler->faecher[$i]['fid'] == $this->fid) {
				$index = $i;
			}
		}
		if ($index > -1) {
			$this->schueler->remove_nachfrage_fach($this->fid);
			$this->schueler->add_nachfrage_fach($this->fid, $this->schueler->faecher[$index]['langfristig'], $this->schueler->faecher[$index]['fachlehrer'], 'neu');
		}
		$return = query_db("DELETE FROM `unterricht` WHERE lid = :lid AND sid = :sid AND fid = :fid", $this->lehrer->get_id(), $this->schueler->get_id(), $this->fid);
		if (!$return) {
			echo "Es ist ein Fehler aufgetreten. Das Paar konnte nicht gelöscht werden";
		}else{
			echo "Das Paar wurde erfolgreich gelöscht.";
		}
		
	}
	
	function add_meeting($sid, $lid, $date, $comment = '') {
		if (!strtotime($date)) {
			echo "<br><br>Bitte gib ein gültiges Datum an.";
		}else {
			$time = strtotime($date);
			$date = date('Y-m-d', $time);
		}
		if (strlen($sid) > 0) {
			$return = query_db("SELECT * FROM nachhilfetreffen WHERE paar_id = :paar_id AND datum = :datum AND sid = :sid AND lid is NULL", $this->paarid, $date, $sid);
		}else{
			$return = query_db("SELECT * FROM nachhilfetreffen WHERE paar_id = :paar_id AND datum = :datum AND sid is NULL AND lid = :lid", $this->paarid, $date, $lid);			
		}
		if (strlen($sid) == 0) {
			$sid = NULL;
		}
		$return = $return->fetch();
		if ($return !== false) {
			die("Es existiert für dieses Nachhilfepaar an diesem Tag schon ein Eintrag von dir!<br><a href=\"index.php?page=customer_meetings".(isset($_GET['customer_id'])?"&customer_id=".$_GET['customer_id']:"")."\" class=\"links2\">Zurück zur Übersicht</a><br><br>");
		}
		if (strlen($sid) > 0) {
			$return = query_db("INSERT INTO nachhilfetreffen (paar_id, sid, lid, bemerkung, datum) VALUES (:paar_id, :sid, NULL, :comment, :datum)",$this->paarid, $sid, $comment, $date);
		}else{
			$return = query_db("INSERT INTO nachhilfetreffen (paar_id, sid, lid, bemerkung, datum) VALUES (:paar_id, NULL, :lid, :comment, :datum)",$this->paarid, $lid, $comment, $date);			
		}
		if ($return) {
			return true;
		}else{
			return false;
		}
	}
	
	function all_meetings($is_person_teacher) {
		if ($is_person_teacher) {
			$return = query_db("SELECT nachhilfetreffen.lid, n2.sid, nachhilfetreffen.datum, nachhilfetreffen.paar_id, nachhilfetreffen.bemerkung as bemerkung_schueler, n2.bemerkung as bemerkung_lehrer FROM `nachhilfetreffen`
				 LEFT JOIN (SELECT * FROM nachhilfetreffen WHERE lid is NULL) as n2 ON n2.paar_id = nachhilfetreffen.paar_id 
				 AND n2.datum = nachhilfetreffen.datum WHERE nachhilfetreffen.paar_id = :paar_id", $this->paarid);						
		}else{
			$return = query_db("SELECT nachhilfetreffen.sid, n2.lid, nachhilfetreffen.datum, nachhilfetreffen.paar_id, nachhilfetreffen.bemerkung as bemerkung_schueler, n2.bemerkung as bemerkung_lehrer FROM `nachhilfetreffen`
				 LEFT JOIN (SELECT * FROM nachhilfetreffen WHERE sid is NULL) as n2 ON n2.paar_id = nachhilfetreffen.paar_id 
				 AND n2.datum = nachhilfetreffen.datum WHERE nachhilfetreffen.paar_id = :paar_id", $this->paarid);
	 	}
 	  if ($return === false) {
		}
		if ($return) {
			return $return;
		}else{
			return false;
		}
		
	}
	
}