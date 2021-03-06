<?php
    defined('__FRAMEWORK3IL__') or die('Acces interdit');
    
    abstract class Datagrid {
        protected $data = null;
        protected $colonnes = null;
        protected $id = "";
        protected $classesCSS = array('datagrid'); 
        private   $ligne;
        
        /**
         * Constructeur initialise les propriétés $data et $colonnes
         * 
         * @param array $data
         * @param array $colonnes
         */
        public function __construct($data, $colonnes) {         
            $this->setData($data);
            $this->setColonnes($colonnes);
        }
        
        /**
         * Setter pour data : alimente le DataGrid en données
         * 
         * @param array $data
         */
        public function setData($data){
            $this->data = $data;
        }
        
        /**
         * Setter pour les colonnes : fournit la description des colonnes du DataGrid
         * 
         * @param array $colonnes
         */
        public function setColonnes($colonnes){
            $this->colonnes = $colonnes;
        }
        
        /**
         * Setter pour l'id du DataGrid à faire figurer dans le code HTML
         * 
         * @param type $id
         */
        public function setId($id){
            $this->id = $id;
        }
        
        /**
         * Ajoute une classe CSS au tableau classeCSS
         * 
         * @param string $classe
         */
        public function ajouterClasse($classe){
            array_push($this->classesCSS, $classe);
        }  
        
        /**
         * Réalise l'affiche de l'attribut id si celui-ci n'est pas null
         */
        public function id() {
            if ($this->id != "") {
                echo 'id="'.$this->id.'"';
            }
        }
        
        /**
         * Réalise l'affichage de l'attribut class à partir du tableau classesCSS
         */
        public function classes() {  
            echo 'class="'.implode(" ", $this->classesCSS).'"';
        }
        
        /**
         * Réalise l'affichage du titre d'une colonne
         * @param string $col
         */
        public function afficherTitreColonne($col) {      
            // Si pas d'indication triable, ou indication non triable, ou pas de DataSet
            // On affiche le titre sans lien
            if (!isset($col['triable']) 
                    || (isset($col['triable']) && $col['triable'] === false) 
                    || !is_a($this->data,'DataSet')) {
                echo $col['titre'];
                return;
            }
            
            $css = array("tri");
            
            $url = HTTPHelper::getParametresURL();            
            $url['direction'] = $this->data->getDirection();
            $url['ordre'] = $col['data'];
            
            // Si c'est la colonne de tri courante
            if ($this->data->getOrdre() == $col['data']){
                // Quelle est la direction courante 
                if ($this->data->getDirection() == 'asc'){
                    $url['direction'] = 'desc';
                    $css[] = 'asc';
                } else {
                    $url['direction'] = 'asc';
                    $css[] = 'desc';
                }
            } else {
                $url['direction'] = 'asc';
            }
            
            $texteURL = '?'.http_build_query($url);
            $classes = implode(' ',$css);
            ?>
            <a href="<?php echo $texteURL;?>" class="<?php echo $classes;?>"><?php echo $col['titre'];?><span></span></a>
            <?php
        }
        
        /**
         * Retourne le numéro de la ligne en cours de rendu
         * @return int
         */
        public function getLigne(){    
            return $this->ligne;
        }
        
        /**
         * Réalise l'affichage du DataGrid
         */
        public function afficher() { 
            ?> 
            <table <?php $this->id(); $this->classes();  ?>>
                <thead>
                    <?php foreach ($this->colonnes as $colonne) { ?>
                    <th>
                        <?php $this->afficherTitreColonne($colonne); ?>
                    </th>
                    <?php } ?>
                </thead>
                <tbody>
                    <?php foreach ($this->data as $coup_de_pouce ) { ?>
                    <tr>
                        <?php foreach ($this->colonnes as $colonne) { ?>
                        <td>
                            <?php if (isset($colonne['rendu'])) {
                                if (method_exists($this, $colonne['rendu'])) {
                                    echo $this->$colonne['rendu']($coup_de_pouce);
                                } else {
                                    throw new Erreur("Ce renderer n'existe pas : ".$colonne['rendu']);
                                }
                            } else {
                                echo $coup_de_pouce[$colonne['data']];
                            } ?>
                        </td>
                        <?php } ?>
                    </tr>
                    <?php $this->ligne++; } ?>
                </tbody>
            </table>
            <?php 
        }      
    }