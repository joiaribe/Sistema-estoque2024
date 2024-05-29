<?php

use Query as Query;
use Dashboard\Call_JS as Call_JS;
use Dashboard\Buttons as Buttons;

class GaleryModel extends GaleryActions {

    public function __construct($param) {
        return $this->whichCall($param);
    }

    private function whichCall($param) {
        switch ($param) {
            case 'albuns':
                $result = $this->BuildAlbuns();
                break;
            case 'LoopImagensModals':
                $result = $this->LoopImagensModals();
                break;
            case 'imagens_itens':
                $result = $this->LoopImagens();
                break;
            case 'LoopCategoryItem':
                $result = $this->LoopCategoryItem();
                break;
            case 'ModalCategory':
                $result = $this->ModalCategory();
                break;
            default:
                $result = NULL;
                parent::__construct();
                break;
        }
        return print $result;
    }

    private function LoopImagensModals() {
        $q = new Query;
        $q
                ->select()
                ->from('gallery_pic')
                ->where_equal_to(
                        array(
                            'id_user' => Session::get('user_id')
                        )
                )
                ->order_by("id_cat asc")
                ->run();
        $data = $q->get_selected();
        $total = $q->get_selected_count();
        if (!($data && $total > 0)) {
            $result = false;
        } else {
            $result = '';
            foreach ($data as $v) {
                $result .= $this->createImagesItensModal($v);
            }
        }
        return $result;
    }

    private function LoopImagens() {
        $q = new Query;
        $q
                ->select()
                ->from('gallery_pic')
                ->where_equal_to(
                        array(
                            'id_user' => Session::get('user_id')
                        )
                )
                ->order_by("id_cat asc")
                ->run();
        $data = $q->get_selected();
        $total = $q->get_selected_count();
        if (!($data && $total > 0)) {
            $result = false;
        } else {
            $result = '';
            foreach ($data as $v) {
                $result .= $this->createImagesItens($v);
            }
        }
        return $result;
    }

}

class GaleryHTML {

    /**
     * Location
     * @var Array 
     */
    var $loc_action = array(
        'add' => false,
        'prev' => false,
        'alt' => false,
        'del' => '/del_cat/'
    );

    /**
     * loop albuns category
     * @access private
     * @return boolean|string
     */
    private function LoopCategory() {
        $q = new Query;
        $q
                ->select()
                ->from('gallery_cat')
                ->where_equal_to(
                        array(
                            'id_user' => Session::get('user_id')
                        )
                )
                ->order_by("title asc")
                ->run();
        $data = $q->get_selected();
        $total = $q->get_selected_count();
        if (!($data && $total > 0)) {
            $result = false;
        } else {
            $result = '';
            foreach ($data as $v) {
                $id = Func::acento($v['title']);
                $result .= '<li><a href="#" data-filter=".' . $id . '">' . $v['title'] . '</a></li>';
            }
        }
        return $result;
    }

    /**
     * loop albuns category item
     * @access private
     * @return boolean|string
     */
    protected function LoopCategoryItem() {
        $q = new Query;
        $q
                ->select()
                ->from('gallery_cat')
                ->where_equal_to(
                        array(
                            'id_user' => Session::get('user_id')
                        )
                )
                ->order_by("title asc")
                ->run();
        $data = $q->get_selected();
        $total = $q->get_selected_count();
        if (!($data && $total > 0)) {
            return false;
        }
        $result = '';
        foreach ($data as $v) {
            $result .= '<option value="' . $v['id'] . '">' . $v['title'] . '</option>';
        }

        return $result;
    }

    /**
     * Build Albuns Category
     * @access protected
     * @return boolean|string
     */
    protected function BuildAlbuns() {
        $data = $this->LoopCategory();
        if (!$data) {
            return false;
        } else {
            return <<<EOF
<ul id="filters" class="media-filter">
    <li><a href="#" data-filter="*"> Todos</a></li>
    {$data}
</ul>
EOF;
        }
    }

    /**
     * create imagens itens for modal
     * @param array $data Query Result
     * @return string
     */
    protected function createImagesItensModal(array $data) {
        $url = URL;
        $full_url = $url . FILENAME . DS;
        $name = Func::array_table('gallery_cat', array('id' => $data['id_cat']), 'title');
        $size = Func::formatBytes($data['size']);

        $name_pic = strlen($data['pic']) >= 21 ? Func::str_truncate($data['pic'], 21) : $data['pic'];
        return <<<EOF
<!-- Modal -->
<div class="modal fade" id="myModal{$data['id']}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Editar Galeria {$name}</h4>
            </div>
            <form action="{$full_url}update_each_item/{$data['id']}" method="post" id="signupForm">
                <div class="modal-body row">

                    <div class="col-md-5 img-modal">
                        <img src="{$url}public/Gallery/{$data['id_user']}/{$data['pic']}" alt="{$data['title']}" title="{$data['title']}">
                        <a href="{$url}public/Gallery/{$data['id_user']}/{$data['pic']}" download="{$url}public/Gallery/{$data['id_user']}/{$data['pic']}" class="btn btn-white btn-sm" title="Baixar Imagem"><i class="fa fa-download"></i> Baixar</a>
                        <a href="{$url}public/Gallery/{$data['id_user']}/{$data['pic']}" target="_blank" class="btn btn-white btn-sm" title="Ver no tamanho original"><i class="fa fa-eye"></i> Tamanho Grande</a>

                        <p class="mtop10"><strong>Nome:</strong> {$name_pic}</p>
                        <p><strong>Extensão:</strong> {$data['type']}</p>
                        <p><strong>Resolução:</strong> {$data['Resolution']}</p>
                        <p><strong>Tamanho:</strong> {$size}</p>
                    </div>
                    <div class="col-md-7">
                        <div class="form-group">
                            <label> Nome</label>
                            <input id="name" readonly value="{$data['pic']}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label> Título</label>
                            <input id="title" name="name" value="{$data['title']}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label> Descrição</label>
                            <textarea name="descri" rows="2" class="form-control">{$data['descri']}</textarea>
                        </div>
                        <div class="form-group">
                            <label> Link URL</label>
                            <input id="link" value="{$url}public/Gallery/{$data['id_user']}/{$data['pic']}" class="form-control">
                        </div>
                        <div class="pull-right">
                            <a href="{$full_url}del_each_pic/{$data['id']}" type="button" class="btn btn-white btn-sm"><i class="fa fa-trash-o"></i> Deletar</a>
                            <button data-dismiss="modal" class="btn btn-default" type="button">Fechar</button>
                            <button class="btn btn-primary" type="submit">Salvar Mudanças</button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
<!-- modal -->


EOF;
    }

    /**
     * Create Imagens Itens
     * @param array $data Query Result
     * @return string
     */
    protected function createImagesItens(array $data) {
        $id = Func::acento(Func::array_table('gallery_cat', array('id' => $data['id_cat']), 'title'));
        $url = URL;
        $input = (Developer\Tools\Url::getURL(3) == 'del_mode') ? '<input type="checkbox" value="' . $data['id'] . '" name="checkbox[]"/>' : NULL;
        return <<<EOF
 <div class="{$id} item " >
    <a href="#myModal{$data['id']}" data-toggle="modal">
        <img src="{$url}public/Gallery/{$data['id_user']}/{$data['pic']}" alt="{$data['title']}" />
    </a>
     
    <p>{$data['title']}  {$input}</p>
</div>
EOF;
    }

    /**
     * Modal Cat update
     * @param array $data Query data
     * @return String
     */
    private function CatModal(array $data) {
        $user_id = Session::get('user_id');
        $url = URL . FILENAME;
        return <<<EOF
<!-- Modal -->
<div class="modal fade" id="CatAltModal{$data['id']}" tabindex="2" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Alterar Categoria : {$data['title']}</h4>
            </div>
            <div class="modal-body">
                <form action="{$url}/alt_cat/{$data['id']}" method="post" id="form_filter" class="form-horizontal ">
                    <div class="form-group">
                        <label> Nome : <span id="field-required">*</span></label>
                        <input class="form-control" id="cname" name="name" minlength="2" value="{$data['title']}" type="text" required />
                        <input type="hidden" value="{$user_id}" name="id_user"/>
                    </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" type="submit">Salvar</button>
                <button data-dismiss="modal" class="btn btn-default" type="button">Fechar</button>
            </div>

            </form>                   
        </div>
    </div>
</div>
<!-- modal -->
EOF;
    }

    private function ItensModalCategoty(array $data) {
        $url = URL . FILENAME;
        return $this->CatModal($data) . <<<EOF
    <tr>
        <td>{$data['id']}</td>
        <td>{$data['title']}</td>
        <td> <a href="#CatAltModal{$data['id']}" data-toggle="modal"  title="Alterar Registro" class="btn-action glyphicons pencil btn-success"><i></i></a>
        {$this->build_buttons($data['id'])}</td>
    </tr>
EOF;
    }

    private function ModalInsertNewCat() {
        $url = URL . FILENAME;
        $user_id = Session::get('user_id');
        return <<<EOF
        <!-- Modal -->
<div class="modal fade" id="NewCat" tabindex="2" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Adicionar Nova Categoria</h4>
            </div>
            <div class="modal-body">
                <form action="{$url}/add_cat" method="post" id="form_filter" class="form-horizontal ">
                    <div class="form-group">
                        <label> Nome : <span id="field-required">*</span></label>
                        <input class="form-control" id="cname" name="name" minlength="2" type="text" required />
                        <input type="hidden" value="{$user_id}" name="id_user"/>
                    </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" type="submit">Salvar</button>
                <button data-dismiss="modal" class="btn btn-default" type="button">Fechar</button>
            </div>

            </form>                   
        </div>
    </div>
</div>
<!-- modal -->
EOF;
    }

    /**
     * Loop modal category
     * @return boolean|string
     */
    private function LoopModalCategory() {
        $q = new Query;
        $q
                ->select()
                ->from('gallery_cat')
                ->where_equal_to(
                        array(
                            'id_user' => Session::get('user_id')
                        )
                )
                ->order_by("title asc")
                ->run();
        $data = $q->get_selected();
        $total = $q->get_selected_count();
        if (!($data && $total > 0)) {
            return false;
        }
        $result = $this->ModalInsertNewCat();
        foreach ($data as $v) {
            $result .= $this->ItensModalCategoty($v);
        }

        return $result;
    }

    /**
     * check what button call
     * @param String $value Value array loc_action
     * @param Integer $id ID reg
     * @return Object
     */
    private function check_buttons($value, $id) {
        $result = false;
        switch ($value) {
            case 'prev':
                $result = Buttons::button_ver(FILENAME . $this->loc_action['prev'] . $id);
                break;
            case 'alt':
                $result = Buttons::button_alt(FILENAME . $this->loc_action['alt'] . $id);
                break;
            case 'del':
                $result = Buttons::button_delete(FILENAME . $this->loc_action['del'] . $id);
                break;
        }
        return $result;
    }

    /**
     * Build Buttons actions
     * @param type $id
     * @return String
     */
    private function build_buttons($id) {
        $result = '';
        foreach ($this->loc_action as $value => $key) {
            if ($key !== false) {
                $result.= ' ' . $this->check_buttons($value, $id) . ' ';
            }
        }
        return $result;
    }

    protected function ModalCategory() {
        return <<<EOF
                <!-- Modal -->
                <div class="modal fade" id="ManagerCategory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title">Gerenciar Categorias</h4>
                            </div>
                                <div class="modal-body row">

                                    <div class="col-md-12">
                                        <section id="unseen">
                                            <table class="table table-bordered table-striped table-condensed">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Nome</th>
                                                        <th class="numeric">Ação</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <p><a href="#NewCat" data-toggle="modal" type="button" class="btn btn-white btn-sm"><i class="fa fa-plus-square"></i> Adicionar Novo</a></p>
                                                    {$this->LoopModalCategory()}
                                                </tbody>
                                            </table>
                                        </section>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
                <!-- modal -->
EOF;
    }

}

class GaleryActions extends GaleryHTML {

    var $location_sucess = 'dashboard/Tools/gallery';

    # plural e singular da página
    var $msg = array(
        'singular' => 'Foto',
        'plural' => 'Fotos',
        'manager' => 'Gerenciar' //palavra que vai aparecer em ferramentas seguido pelo nome da página
    );

    /**
     * Main Clients
     * @var String 
     */
    var $page = 'gallery';

    public function __construct() {
        switch (Url::getURL(3)) {
            case 'insert':
                $this->Insert();
                break;
            case 'del_cat':
                $this->DeleteCat();
                break;
            case 'delete_all':
                $this->DeleteAllPics();
                break;
            case 'alt_cat':
                $this->updateCat();
                break;
            case 'add_cat':
                $this->addNewCat();
                break;
            case 'del_each_pic':
                $this->deleteEachPic();
                break;
            case 'update_each_item':
                $this->updateEachPic();
                break;
            default:
                break;
        }
    }

    /**
     * Delete each picture
     * 
     * @access private
     * @return void
     */
    private function deleteEachPic() {
        $q = new Query();
        $q
                ->delete('gallery_pic')
                ->where_equal_to(
                        array('id' => Url::getURL(4))
                )
                ->run();
        if ($q) {
            $mensagem = $this->msg['singular'] . ' excluida com sucesso !';
            Call_JS::alerta($mensagem);
            Call_JS::retornar(URL . 'dashboard/Tools/' . $this->page);
        }
    }

    /**
     * Update category
     * 
     * @access private
     * @return void
     */
    private function addNewCat() {
        $q = new Query;
        $q
                ->insert_into('gallery_cat', array(
                    'title' => $_POST['name'],
                    'id_user' => $_POST['id_user'])
                )
                ->run();
        if (!$q) {
            all_JS::alerta('Não foi possivel inserir uma nova categoria !');
            Call_JS::retornar(URL . $this->location_sucess);
        }

        Call_JS::alerta('Categoria adicionada com sucesso !');
        Call_JS::retornar(URL . $this->location_sucess);
    }

    /**
     * Update each pic
     * 
     * @access private
     * @return void
     */
    private function updateEachPic() {
        $q = new Query;
        $q
                ->update('gallery_pic', array(
                    'title' => $_POST['name'],
                    'descri' => $_POST['descri']
                        )
                )
                ->where_equal_to(
                        array(
                            'id' => Url::getURL(4)
                        )
                )
                ->run();
        if (!$q) {
            all_JS::alerta('Não foi possivel alterar a foto !');
            Call_JS::retornar(URL . $this->location_sucess);
        }

        Call_JS::alerta('Foto alterada com sucesso !');
        Call_JS::retornar(URL . $this->location_sucess);
    }

    /**
     * Update category
     * 
     * @access private
     * @return void
     */
    private function updateCat() {
        $q = new Query;
        $q
                ->update('gallery_cat', array('title' => $_POST['name']))
                ->where_equal_to(
                        array(
                            'id' => Url::getURL(4)
                        )
                )
                ->run();
        if (!$q) {
            all_JS::alerta('Não foi possivel alterar a categoria !');
            Call_JS::retornar(URL . $this->location_sucess);
        }

        Call_JS::alerta('Categoria alterada com sucesso !');
        Call_JS::retornar(URL . $this->location_sucess);
    }

    /**
     * Upload file
     */
    private function Insert() {
        $upload = new Upload('upl');
        $upload
                ->file_name(true)
                ->upload_to('/opt/lampp/htdocs/Salao_4.2/uploads/')
                ->run();
        if (!$upload->was_uploaded) {
            die('error : ' . $upload->error);
        } else {
            echo 'image sent successfully !';
        }
    }

    /**
     * Delete multiple pictures
     * 
     * @access private
     * @return void
     */
    private function DeleteAllPics() {
        if (isset($_POST['delete']) or isset($_POST['checkbox'])) {
            //store the array of checkbox values
            $allCheckBoxId = $_POST['checkbox']; //filter_input(INPUT_POST, 'checkbox');
            $this->run_query($allCheckBoxId);
        } else {
            Call_JS::alerta('Selecione pelo menos um ' . $this->msg['singular']);
            Call_JS::retornar(URL . 'dashboard/Manager/' . $this->page);
        }
    }

    /**
     * monta query de delete
     * @access private
     * @return void
     */
    private function run_query($ids) {
        $q = new Query();
        $q
                ->delete('gallery_pic')
                ->where_in(
                        array('id' => $ids)
                )
                ->run();
        if ($q) {
            $total = count($ids);
            $mensagem = ($total == 1) ? '1 ' . $this->msg['singular'] . ' excluida com sucesso !' : $total . ' ' . $this->msg['plural'] . ' excluidas com sucesso !';
            Call_JS::alerta($mensagem);
            Call_JS::retornar(URL . 'dashboard/Tools/' . $this->page);
        }
    }

    private function DeleteCat() {
        $q = new Query;
        $q
                ->delete_from('gallery_cat')
                ->where_equal_to(
                        array(
                            'id' => Url::getURL(4)
                        )
                )
                ->run();
        if (!$q) {
            return false;
        }

        Call_JS::alerta('Categoria deletada com sucesso !');
        Call_JS::retornar(URL . $this->location_sucess);
    }

}
