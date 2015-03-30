<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Livre extends CI_Controller {
    const NB_COMMENTAIRE_PAR_PAGE = 2;
    public function __construct () {
        parent::__construct();
        // Chargement des resources pour tout le controller
        $this->load->helper (array('url', 'assets'));
        $this->load->model ('livre_model', 'livreManager');
    }
    public function index ($g_nb_commentaire = 1) {
        $this->voir ($g_nb_commentaire);
    }
    public function voir($g_nb_commentaire = 1)
    {
        $this->load->library('pagination');

        $data = array();
        //	Récupération du nombre total de messages sauvegardés dans la base de données
        $nb_commentaire_total = $this->livreManager->count();

        //	On vérifie la cohérence de la variable $_GET
        if($g_nb_commentaire > 1)
        {
            //	La variable $_GET semblent être correcte. On doit maintenant
            //	vérifier s'il y a bien assez de commentaires dans la base de données.
            if($g_nb_commentaire <= $nb_commentaire_total)
            {
                //	Il y a assez de commentaires dans la base de données.
                //	La variable $_GET est donc cohérente.

                $nb_commentaire = intval($g_nb_commentaire);
            }
            else
            {
                //	Il n'y pas assez de messages dans la base de données.

                $nb_commentaire = 1;
            }
        }
        else
        {
            //	La variable $_GET "nb_commentaire" est erronée. On lui donne une
            //	valeur par défaut.

            $nb_commentaire = 1;
        }
        $config = array();

        $config['base_url']         = base_url() . 'livre/voir/';
        $config['total_rows']       = $nb_commentaire_total;
        $config['per_page']         = self::NB_COMMENTAIRE_PAR_PAGE;
        //	Mise en place de la pagination
        $this->pagination->initialize($config);

        $data['pagination'] = $this->pagination->create_links();
        $data['nb_commentaires'] = $nb_commentaire_total;

        //	Maintenant que l'on connaît le numéro du commentaire, on peut lancer
        //	la requête récupérant les commentaires dans la base de données.
        $data['messages'] = $this->livreManager->get_commentaires(self::NB_COMMENTAIRE_PAR_PAGE, $nb_commentaire-1);

        //	On charge la vue
        $this->load->view('livre/afficher_commentaires', $data);
    }
    public function ecrire () {
        $this->load->library('form_validation');
        //  Cette méthode permet de changer les délimiteurs par défaut des messages d'erreur (<p></p>).
        $this->form_validation->set_error_delimiters('<p class="form_erreur">', '</p>');
        //  Mise en place des règles de validation du formulaire
        //  Nombre de caractères : [3,25] pour le pseudo et [3,3000] pour le commentaire
        //  Uniquement des caractères alphanumériques, des tirets et des underscores pour le pseudo

        $this->form_validation->set_rules('pseudo',  '"Pseudo"',  'trim|required|min_length[3]|max_length[25]|alpha_dash');
        $this->form_validation->set_rules('contenu', '"Contenu"', 'trim|required|min_length[3]|max_length[3000]');
        if($this->form_validation->run()) {

            //  Nous disposons d'un pseudo et d'un commentaire sous une bonne forme
            //  Sauvegarde du commentaire dans la base de données
            $this->livreManager->ajouter_commentaire($this->input->post('pseudo'), $this->input->post('contenu'));

            //  Affichage de la confirmation
            $this->load->view('livre/confirmation');

        } else {
            $this->load->view('livre/ecrire_commentaire');
        }

    }
}