<?php
class paar {
	public $schueler = schueler::class;
	public $lehrer = lehrer::class;
	public $raum;
	public $tag;
	public $anfang;
	public $ende;
	public $fid;
	public $paarid;
	public $erstellungstag;
	public $lehrer_dokument;
	public $schueler_dokument;
	function __construct(int $paar_id, int $lid = -1, int $sid = -1) {
		if ($lid == -1 && $sid == -1) {
			$return = query_db("SELECT * FROM `unterricht` WHERE id = :id", $paar_id);
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
					$this->raum = $return['treff_raum'];
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
			$return = query_db("SELECT * FROM `unterricht` WHERE lid = :lid AND sid = :sid", $lid, $sid);
			if ($return) {
				$return = $return->fetch();
				if ($return) {
					require 'includes/class_lehrer.php';
					require 'includes/class_schueler.php';
					$this->paarid = $return['id'];
					$this->schueler = new schueler(-1, $return['sid']);
					$this->lehrer = new lehrer(-1, $return['lid']);
					$this->raum = $return['treff_raum'];
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
}