<?php
class Livre_model extends CI_Model {
    private $table = 'commentaires';
    public function ajouter_commentaire ($pseudo, $message) {
        if ( ! is_string($pseudo) OR ! is_string($message) OR empty($pseudo) OR empty($message) ) {
          return false;
        }
        return $this->db->set(array(
            'pseudo'  => $pseudo,
            'message' => $message))
            ->set('date', 'NOW()', false)
            ->insert($this->table);
    }
    public function count () {
        return $this->db->count_all($this->table);
    }
    public function get_commentaires($nb, $debut = 0) {
        if( ! is_integer($nb) OR $nb < 1 OR ! is_integer($debut) OR $debut < 0) {
            return false;
        }
        return $this->db->select('`id`, `pseudo`, `message`, DATE_FORMAT(`date`,\'%d/%m/%Y &agrave; %H:%i:%s\') AS \'date\'', false)
            ->from($this->table)
            ->order_by('id', 'desc')
            ->limit($nb, $debut)
            ->get()
            ->result();
    }
}