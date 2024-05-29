<?php

namespace Settings\Menu;

use Dashboard\Buttons as Buttons;
use Query as Query;
use Developer\Tools\Url as Url;

/**
 * HTML used in all class
 */
class MenuHTML extends requireds {

    /**
     * html header of table
     * @return string
     */
    protected function body_table() {
        return '<thead>
                    <tr>
                        <th>Estado</th>
                        <th>Posição</th>
                        <th>Ícones</th>
                        <th>Nome</th>
                        <th>Submenu</th>
                        <th>Ação</th>
                    </tr>
                </thead>';
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

    /**
     * Check data is empty
     * @param mixed $data
     * @param string $txt
     * @return mixed
     */
    private function check_field($data, $txt = '<font color="red"><b>Não Encontrado !</b></font>') {
        if (isset($data) && $data !== '') {
            return $data;
        }
        return $txt;
    }

    private function check_sub($sub) {
        return $sub == true ? '<span class="badge bg-info">Ativado</span>' : '<span class="badge bg-important">Desativado</span>';
    }

    private function check_status($status) {
        return $status == true ? '<span class="badge bg-success">Ativo</span>' : '<span class="badge bg-warning">Inativo</span>';
    }

    /**
     * html conteudo da tabela
     * @access protected
     * @return string
     */
    protected function contain_table(array $fetch) {
        return <<<EOFPAGE
        <tr class="gradeX">
            <td>{$this->check_status($fetch['status'])}</td>
            <td>{$fetch['position']}</td>
            <td title="{$fetch['icone']}"><i class="fa {$fetch['icone']}"></i></td>
            <td>{$fetch['name']}</td>
            <td>{$this->check_sub($fetch['sub'])}</td>
            <td class="right actions">{$this->build_buttons($fetch['id'])}</td>
        </tr>
EOFPAGE;
    }

    /**
     * Verify access of account type
     * @param String $data Access Data Currenty
     * @return boolean
     */
    private function CheckVisible($data) {
        $myAccountType = \GetInfo::_user_cargo(NULL, FALSE);
        $total = \Func::_contarReg('menu_sub_access', array('id_sub_menu' => $data['id']));
        // is admin ?
        if ($myAccountType == 0) {
            return true;
        }
        // check access
        if ($total > 0) {
            return true;
        }
    }

    /**
     * make a loop in tools
     * @param array $ids
     * @return string
     */
    private function tools_call(array $ids) {
        $result = '';
        $q = new Query;
        $q
                ->select()
                ->from('menu_sub')
                ->where_in(
                        array('id' => $ids)
                )
                ->run();
        if ($q) {
            $data = $q->get_selected();
            foreach ($data as $dados) {
                $url = URL . $dados['link'];
                if ($this->CheckVisible($dados)) {
                    $result.= '<li><a title="' . $this->msg['manager'] . ' ' . $dados['name'] . '" href="' . $url . '">' . $this->msg['manager'] . ' ' . $dados['name'] . '</a></li>';
                }
            }
            return $result;
        }
    }

    protected function ModalIcones() {
        return <<<EOFPAGE
<!-- Modal -->
<div class="modal fade" id="listIcons" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Lista de Ícones</h4>
            </div>
            <div class="modal-body">

    <div class="fontawesome-icon-list">

    <div id="new">
        <h2 class="page-header">11 Novos Ícones 4.0</h2>
        <div class="row fontawesome-icon-list">

            <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-rub"></i> fa-rub</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-ruble"></i> fa-ruble <span class="text-muted">(alias)</span></a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-rouble"></i> fa-rouble <span class="text-muted">(alias)</span></a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-pagelines"></i> fa-pagelines</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-stack-exchange"></i> fa-stack-exchange</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-arrow-circle-o-right"></i> fa-arrow-circle-o-right</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-arrow-circle-o-left"></i> fa-arrow-circle-o-left</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-caret-square-o-left"></i> fa-caret-square-o-left</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-toggle-left"></i> fa-toggle-left <span class="text-muted">(alias)</span></a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-dot-circle-o"></i> fa-dot-circle-o</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-wheelchair"></i> fa-wheelchair</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-vimeo-square"></i> fa-vimeo-square</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-try"></i> fa-try</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-turkish-lira"></i> fa-turkish-lira <span class="text-muted">(alias)</span></a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-plus-square-o"></i> fa-plus-square-o</a></div>

        </div>
    </div>

    <section id="web-application">
    <h2 class="page-header">Ícones Aplicações Web</h2>

    <div class="row fontawesome-icon-list">



    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-adjust"></i> fa-adjust</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-anchor"></i> fa-anchor</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-archive"></i> fa-archive</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-arrows"></i> fa-arrows</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-arrows-h"></i> fa-arrows-h</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-arrows-v"></i> fa-arrows-v</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-asterisk"></i> fa-asterisk</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-ban"></i> fa-ban</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-bar-chart-o"></i> fa-bar-chart-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-barcode"></i> fa-barcode</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-bars"></i> fa-bars</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-beer"></i> fa-beer</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-bell"></i> fa-bell</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-bell-o"></i> fa-bell-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-bolt"></i> fa-bolt</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-book"></i> fa-book</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-bookmark"></i> fa-bookmark</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-bookmark-o"></i> fa-bookmark-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-briefcase"></i> fa-briefcase</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-bug"></i> fa-bug</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-building-o"></i> fa-building-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-bullhorn"></i> fa-bullhorn</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-bullseye"></i> fa-bullseye</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-calendar"></i> fa-calendar</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-calendar-o"></i> fa-calendar-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-camera"></i> fa-camera</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-camera-retro"></i> fa-camera-retro</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-caret-square-o-down"></i> fa-caret-square-o-down</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-caret-square-o-left"></i> fa-caret-square-o-left</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-caret-square-o-right"></i> fa-caret-square-o-right</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-caret-square-o-up"></i> fa-caret-square-o-up</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-certificate"></i> fa-certificate</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-check"></i> fa-check</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-check-circle"></i> fa-check-circle</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-check-circle-o"></i> fa-check-circle-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-check-square"></i> fa-check-square</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-check-square-o"></i> fa-check-square-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-circle"></i> fa-circle</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-circle-o"></i> fa-circle-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-clock-o"></i> fa-clock-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-cloud"></i> fa-cloud</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-cloud-download"></i> fa-cloud-download</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-cloud-upload"></i> fa-cloud-upload</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-code"></i> fa-code</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-code-fork"></i> fa-code-fork</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-coffee"></i> fa-coffee</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-cog"></i> fa-cog</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-cogs"></i> fa-cogs</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-comment"></i> fa-comment</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-comment-o"></i> fa-comment-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-comments"></i> fa-comments</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-comments-o"></i> fa-comments-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-compass"></i> fa-compass</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-credit-card"></i> fa-credit-card</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-crop"></i> fa-crop</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-crosshairs"></i> fa-crosshairs</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-cutlery"></i> fa-cutlery</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-dashboard"></i> fa-dashboard <span class="text-muted">(alias)</span></a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-desktop"></i> fa-desktop</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-dot-circle-o"></i> fa-dot-circle-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-download"></i> fa-download</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-edit"></i> fa-edit <span class="text-muted">(alias)</span></a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-ellipsis-h"></i> fa-ellipsis-h</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-ellipsis-v"></i> fa-ellipsis-v</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-envelope"></i> fa-envelope</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-envelope-o"></i> fa-envelope-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-eraser"></i> fa-eraser</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-exchange"></i> fa-exchange</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-exclamation"></i> fa-exclamation</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-exclamation-circle"></i> fa-exclamation-circle</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-exclamation-triangle"></i> fa-exclamation-triangle</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-external-link"></i> fa-external-link</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-external-link-square"></i> fa-external-link-square</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-eye"></i> fa-eye</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-eye-slash"></i> fa-eye-slash</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-female"></i> fa-female</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-fighter-jet"></i> fa-fighter-jet</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-film"></i> fa-film</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-filter"></i> fa-filter</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-fire"></i> fa-fire</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-fire-extinguisher"></i> fa-fire-extinguisher</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-flag"></i> fa-flag</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-flag-checkered"></i> fa-flag-checkered</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-flag-o"></i> fa-flag-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-flash"></i> fa-flash <span class="text-muted">(alias)</span></a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-flask"></i> fa-flask</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-folder"></i> fa-folder</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-folder-o"></i> fa-folder-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-folder-open"></i> fa-folder-open</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-folder-open-o"></i> fa-folder-open-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-frown-o"></i> fa-frown-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-gamepad"></i> fa-gamepad</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-gavel"></i> fa-gavel</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-gear"></i> fa-gear <span class="text-muted">(alias)</span></a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-gears"></i> fa-gears <span class="text-muted">(alias)</span></a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-gift"></i> fa-gift</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-glass"></i> fa-glass</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-globe"></i> fa-globe</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-group"></i> fa-group <span class="text-muted">(alias)</span></a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-hdd-o"></i> fa-hdd-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-headphones"></i> fa-headphones</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-heart"></i> fa-heart</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-heart-o"></i> fa-heart-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-home"></i> fa-home</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-inbox"></i> fa-inbox</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#"><i class="fa fa-info"></i> fa-info</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#info-circle"><i class="fa fa-info-circle"></i> fa-info-circle</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#key"><i class="fa fa-key"></i> fa-key</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#keyboard-o"><i class="fa fa-keyboard-o"></i> fa-keyboard-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#laptop"><i class="fa fa-laptop"></i> fa-laptop</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#leaf"><i class="fa fa-leaf"></i> fa-leaf</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#gavel"><i class="fa fa-legal"></i> fa-legal <span class="text-muted">(alias)</span></a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#lemon-o"><i class="fa fa-lemon-o"></i> fa-lemon-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#level-down"><i class="fa fa-level-down"></i> fa-level-down</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#level-up"><i class="fa fa-level-up"></i> fa-level-up</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#lightbulb-o"><i class="fa fa-lightbulb-o"></i> fa-lightbulb-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#location-arrow"><i class="fa fa-location-arrow"></i> fa-location-arrow</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#lock"><i class="fa fa-lock"></i> fa-lock</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#magic"><i class="fa fa-magic"></i> fa-magic</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#magnet"><i class="fa fa-magnet"></i> fa-magnet</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#share"><i class="fa fa-mail-forward"></i> fa-mail-forward <span class="text-muted">(alias)</span></a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#reply"><i class="fa fa-mail-reply"></i> fa-mail-reply <span class="text-muted">(alias)</span></a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#mail-reply-all"><i class="fa fa-mail-reply-all"></i> fa-mail-reply-all</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#male"><i class="fa fa-male"></i> fa-male</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#map-marker"><i class="fa fa-map-marker"></i> fa-map-marker</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#meh-o"><i class="fa fa-meh-o"></i> fa-meh-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#microphone"><i class="fa fa-microphone"></i> fa-microphone</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#microphone-slash"><i class="fa fa-microphone-slash"></i> fa-microphone-slash</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#minus"><i class="fa fa-minus"></i> fa-minus</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#minus-circle"><i class="fa fa-minus-circle"></i> fa-minus-circle</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#minus-square"><i class="fa fa-minus-square"></i> fa-minus-square</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#minus-square-o"><i class="fa fa-minus-square-o"></i> fa-minus-square-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#mobile"><i class="fa fa-mobile"></i> fa-mobile</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#mobile"><i class="fa fa-mobile-phone"></i> fa-mobile-phone <span class="text-muted">(alias)</span></a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#money"><i class="fa fa-money"></i> fa-money</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#moon-o"><i class="fa fa-moon-o"></i> fa-moon-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#music"><i class="fa fa-music"></i> fa-music</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#pencil"><i class="fa fa-pencil"></i> fa-pencil</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#pencil-square"><i class="fa fa-pencil-square"></i> fa-pencil-square</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#pencil-square-o"><i class="fa fa-pencil-square-o"></i> fa-pencil-square-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#phone"><i class="fa fa-phone"></i> fa-phone</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#phone-square"><i class="fa fa-phone-square"></i> fa-phone-square</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#picture-o"><i class="fa fa-picture-o"></i> fa-picture-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#plane"><i class="fa fa-plane"></i> fa-plane</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#plus"><i class="fa fa-plus"></i> fa-plus</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#plus-circle"><i class="fa fa-plus-circle"></i> fa-plus-circle</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#plus-square"><i class="fa fa-plus-square"></i> fa-plus-square</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#plus-square-o"><i class="fa fa-plus-square-o"></i> fa-plus-square-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#power-off"><i class="fa fa-power-off"></i> fa-power-off</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#print"><i class="fa fa-print"></i> fa-print</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#puzzle-piece"><i class="fa fa-puzzle-piece"></i> fa-puzzle-piece</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#qrcode"><i class="fa fa-qrcode"></i> fa-qrcode</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#question"><i class="fa fa-question"></i> fa-question</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#question-circle"><i class="fa fa-question-circle"></i> fa-question-circle</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#quote-left"><i class="fa fa-quote-left"></i> fa-quote-left</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#quote-right"><i class="fa fa-quote-right"></i> fa-quote-right</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#random"><i class="fa fa-random"></i> fa-random</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#refresh"><i class="fa fa-refresh"></i> fa-refresh</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#reply"><i class="fa fa-reply"></i> fa-reply</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#reply-all"><i class="fa fa-reply-all"></i> fa-reply-all</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#retweet"><i class="fa fa-retweet"></i> fa-retweet</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#road"><i class="fa fa-road"></i> fa-road</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#rocket"><i class="fa fa-rocket"></i> fa-rocket</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#rss"><i class="fa fa-rss"></i> fa-rss</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#rss-square"><i class="fa fa-rss-square"></i> fa-rss-square</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#search"><i class="fa fa-search"></i> fa-search</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#search-minus"><i class="fa fa-search-minus"></i> fa-search-minus</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#search-plus"><i class="fa fa-search-plus"></i> fa-search-plus</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#share"><i class="fa fa-share"></i> fa-share</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#share-square"><i class="fa fa-share-square"></i> fa-share-square</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#share-square-o"><i class="fa fa-share-square-o"></i> fa-share-square-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#shield"><i class="fa fa-shield"></i> fa-shield</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#shopping-cart"><i class="fa fa-shopping-cart"></i> fa-shopping-cart</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#sign-in"><i class="fa fa-sign-in"></i> fa-sign-in</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#sign-out"><i class="fa fa-sign-out"></i> fa-sign-out</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#signal"><i class="fa fa-signal"></i> fa-signal</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#sitemap"><i class="fa fa-sitemap"></i> fa-sitemap</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#smile-o"><i class="fa fa-smile-o"></i> fa-smile-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#sort"><i class="fa fa-sort"></i> fa-sort</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#sort-alpha-asc"><i class="fa fa-sort-alpha-asc"></i> fa-sort-alpha-asc</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#sort-alpha-desc"><i class="fa fa-sort-alpha-desc"></i> fa-sort-alpha-desc</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#sort-amount-asc"><i class="fa fa-sort-amount-asc"></i> fa-sort-amount-asc</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#sort-amount-desc"><i class="fa fa-sort-amount-desc"></i> fa-sort-amount-desc</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#sort-asc"><i class="fa fa-sort-asc"></i> fa-sort-asc</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#sort-desc"><i class="fa fa-sort-desc"></i> fa-sort-desc</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#sort-asc"><i class="fa fa-sort-down"></i> fa-sort-down <span class="text-muted">(alias)</span></a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#sort-numeric-asc"><i class="fa fa-sort-numeric-asc"></i> fa-sort-numeric-asc</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#sort-numeric-desc"><i class="fa fa-sort-numeric-desc"></i> fa-sort-numeric-desc</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#sort-desc"><i class="fa fa-sort-up"></i> fa-sort-up <span class="text-muted">(alias)</span></a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#spinner"><i class="fa fa-spinner"></i> fa-spinner</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#square"><i class="fa fa-square"></i> fa-square</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#square-o"><i class="fa fa-square-o"></i> fa-square-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#star"><i class="fa fa-star"></i> fa-star</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#star-half"><i class="fa fa-star-half"></i> fa-star-half</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#star-half-o"><i class="fa fa-star-half-empty"></i> fa-star-half-empty <span class="text-muted">(alias)</span></a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#star-half-o"><i class="fa fa-star-half-full"></i> fa-star-half-full <span class="text-muted">(alias)</span></a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#star-half-o"><i class="fa fa-star-half-o"></i> fa-star-half-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#star-o"><i class="fa fa-star-o"></i> fa-star-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#subscript"><i class="fa fa-subscript"></i> fa-subscript</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#suitcase"><i class="fa fa-suitcase"></i> fa-suitcase</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#sun-o"><i class="fa fa-sun-o"></i> fa-sun-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#superscript"><i class="fa fa-superscript"></i> fa-superscript</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#tablet"><i class="fa fa-tablet"></i> fa-tablet</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#tachometer"><i class="fa fa-tachometer"></i> fa-tachometer</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#tag"><i class="fa fa-tag"></i> fa-tag</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#tags"><i class="fa fa-tags"></i> fa-tags</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#tasks"><i class="fa fa-tasks"></i> fa-tasks</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#terminal"><i class="fa fa-terminal"></i> fa-terminal</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#thumb-tack"><i class="fa fa-thumb-tack"></i> fa-thumb-tack</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#thumbs-down"><i class="fa fa-thumbs-down"></i> fa-thumbs-down</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#thumbs-o-down"><i class="fa fa-thumbs-o-down"></i> fa-thumbs-o-down</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#thumbs-o-up"><i class="fa fa-thumbs-o-up"></i> fa-thumbs-o-up</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#thumbs-up"><i class="fa fa-thumbs-up"></i> fa-thumbs-up</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#ticket"><i class="fa fa-ticket"></i> fa-ticket</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#times"><i class="fa fa-times"></i> fa-times</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#times-circle"><i class="fa fa-times-circle"></i> fa-times-circle</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#times-circle-o"><i class="fa fa-times-circle-o"></i> fa-times-circle-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#tint"><i class="fa fa-tint"></i> fa-tint</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#caret-square-o-down"><i class="fa fa-toggle-down"></i> fa-toggle-down <span class="text-muted">(alias)</span></a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#caret-square-o-left"><i class="fa fa-toggle-left"></i> fa-toggle-left <span class="text-muted">(alias)</span></a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#caret-square-o-right"><i class="fa fa-toggle-right"></i> fa-toggle-right <span class="text-muted">(alias)</span></a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#caret-square-o-up"><i class="fa fa-toggle-up"></i> fa-toggle-up <span class="text-muted">(alias)</span></a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#trash-o"><i class="fa fa-trash-o"></i> fa-trash-o</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#trophy"><i class="fa fa-trophy"></i> fa-trophy</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#truck"><i class="fa fa-truck"></i> fa-truck</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#umbrella"><i class="fa fa-umbrella"></i> fa-umbrella</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#unlock"><i class="fa fa-unlock"></i> fa-unlock</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#unlock-alt"><i class="fa fa-unlock-alt"></i> fa-unlock-alt</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#sort"><i class="fa fa-unsorted"></i> fa-unsorted <span class="text-muted">(alias)</span></a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#upload"><i class="fa fa-upload"></i> fa-upload</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#user"><i class="fa fa-user"></i> fa-user</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#users"><i class="fa fa-users"></i> fa-users</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#video-camera"><i class="fa fa-video-camera"></i> fa-video-camera</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#volume-down"><i class="fa fa-volume-down"></i> fa-volume-down</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#volume-off"><i class="fa fa-volume-off"></i> fa-volume-off</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#volume-up"><i class="fa fa-volume-up"></i> fa-volume-up</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#exclamation-triangle"><i class="fa fa-warning"></i> fa-warning <span class="text-muted">(alias)</span></a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#wheelchair"><i class="fa fa-wheelchair"></i> fa-wheelchair</a></div>

    <div class="fa-hover col-md-3 col-sm-4"><a href="#wrench"><i class="fa fa-wrench"></i> fa-wrench</a></div>

    </div>

    </section>

    <section id="form-control">
        <h2 class="page-header">Ícones de Controle</h2>

        <div class="row fontawesome-icon-list">



            <div class="fa-hover col-md-3 col-sm-4"><a href="#check-square"><i class="fa fa-check-square"></i> fa-check-square</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#check-square-o"><i class="fa fa-check-square-o"></i> fa-check-square-o</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#circle"><i class="fa fa-circle"></i> fa-circle</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#circle-o"><i class="fa fa-circle-o"></i> fa-circle-o</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#dot-circle-o"><i class="fa fa-dot-circle-o"></i> fa-dot-circle-o</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#minus-square"><i class="fa fa-minus-square"></i> fa-minus-square</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#minus-square-o"><i class="fa fa-minus-square-o"></i> fa-minus-square-o</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#plus-square"><i class="fa fa-plus-square"></i> fa-plus-square</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#plus-square-o"><i class="fa fa-plus-square-o"></i> fa-plus-square-o</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#square"><i class="fa fa-square"></i> fa-square</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#square-o"><i class="fa fa-square-o"></i> fa-square-o</a></div>

        </div>
    </section>

    <section id="currency">
        <h2 class="page-header">Ícones Dinheiro</h2>

        <div class="row fontawesome-icon-list">



            <div class="fa-hover col-md-3 col-sm-4"><a href="#btc"><i class="fa fa-bitcoin"></i> fa-bitcoin <span class="text-muted">(alias)</span></a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#btc"><i class="fa fa-btc"></i> fa-btc</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#jpy"><i class="fa fa-cny"></i> fa-cny <span class="text-muted">(alias)</span></a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#usd"><i class="fa fa-dollar"></i> fa-dollar <span class="text-muted">(alias)</span></a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#eur"><i class="fa fa-eur"></i> fa-eur</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#eur"><i class="fa fa-euro"></i> fa-euro <span class="text-muted">(alias)</span></a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#gbp"><i class="fa fa-gbp"></i> fa-gbp</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#inr"><i class="fa fa-inr"></i> fa-inr</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#jpy"><i class="fa fa-jpy"></i> fa-jpy</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#krw"><i class="fa fa-krw"></i> fa-krw</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#money"><i class="fa fa-money"></i> fa-money</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#jpy"><i class="fa fa-rmb"></i> fa-rmb <span class="text-muted">(alias)</span></a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#rub"><i class="fa fa-rouble"></i> fa-rouble <span class="text-muted">(alias)</span></a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#rub"><i class="fa fa-rub"></i> fa-rub</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#rub"><i class="fa fa-ruble"></i> fa-ruble <span class="text-muted">(alias)</span></a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#inr"><i class="fa fa-rupee"></i> fa-rupee <span class="text-muted">(alias)</span></a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#try"><i class="fa fa-try"></i> fa-try</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#try"><i class="fa fa-turkish-lira"></i> fa-turkish-lira <span class="text-muted">(alias)</span></a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#usd"><i class="fa fa-usd"></i> fa-usd</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#krw"><i class="fa fa-won"></i> fa-won <span class="text-muted">(alias)</span></a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#jpy"><i class="fa fa-yen"></i> fa-yen <span class="text-muted">(alias)</span></a></div>

        </div>

    </section>

    <section id="text-editor">
        <h2 class="page-header">Ícones Editor de Texto</h2>

        <div class="row fontawesome-icon-list">



            <div class="fa-hover col-md-3 col-sm-4"><a href="#align-center"><i class="fa fa-align-center"></i> fa-align-center</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#align-justify"><i class="fa fa-align-justify"></i> fa-align-justify</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#align-left"><i class="fa fa-align-left"></i> fa-align-left</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#align-right"><i class="fa fa-align-right"></i> fa-align-right</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#bold"><i class="fa fa-bold"></i> fa-bold</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#link"><i class="fa fa-chain"></i> fa-chain <span class="text-muted">(alias)</span></a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#chain-broken"><i class="fa fa-chain-broken"></i> fa-chain-broken</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#clipboard"><i class="fa fa-clipboard"></i> fa-clipboard</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#columns"><i class="fa fa-columns"></i> fa-columns</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#files-o"><i class="fa fa-copy"></i> fa-copy <span class="text-muted">(alias)</span></a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#scissors"><i class="fa fa-cut"></i> fa-cut <span class="text-muted">(alias)</span></a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#outdent"><i class="fa fa-dedent"></i> fa-dedent <span class="text-muted">(alias)</span></a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#eraser"><i class="fa fa-eraser"></i> fa-eraser</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#file"><i class="fa fa-file"></i> fa-file</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#file-o"><i class="fa fa-file-o"></i> fa-file-o</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#file-text"><i class="fa fa-file-text"></i> fa-file-text</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#file-text-o"><i class="fa fa-file-text-o"></i> fa-file-text-o</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#files-o"><i class="fa fa-files-o"></i> fa-files-o</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#floppy-o"><i class="fa fa-floppy-o"></i> fa-floppy-o</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#font"><i class="fa fa-font"></i> fa-font</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#indent"><i class="fa fa-indent"></i> fa-indent</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#italic"><i class="fa fa-italic"></i> fa-italic</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#link"><i class="fa fa-link"></i> fa-link</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#list"><i class="fa fa-list"></i> fa-list</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#list-alt"><i class="fa fa-list-alt"></i> fa-list-alt</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#list-ol"><i class="fa fa-list-ol"></i> fa-list-ol</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#list-ul"><i class="fa fa-list-ul"></i> fa-list-ul</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#outdent"><i class="fa fa-outdent"></i> fa-outdent</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#paperclip"><i class="fa fa-paperclip"></i> fa-paperclip</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#clipboard"><i class="fa fa-paste"></i> fa-paste <span class="text-muted">(alias)</span></a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#repeat"><i class="fa fa-repeat"></i> fa-repeat</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#undo"><i class="fa fa-rotate-left"></i> fa-rotate-left <span class="text-muted">(alias)</span></a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#repeat"><i class="fa fa-rotate-right"></i> fa-rotate-right <span class="text-muted">(alias)</span></a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#floppy-o"><i class="fa fa-save"></i> fa-save <span class="text-muted">(alias)</span></a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#scissors"><i class="fa fa-scissors"></i> fa-scissors</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#strikethrough"><i class="fa fa-strikethrough"></i> fa-strikethrough</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#table"><i class="fa fa-table"></i> fa-table</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#text-height"><i class="fa fa-text-height"></i> fa-text-height</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#text-width"><i class="fa fa-text-width"></i> fa-text-width</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#th"><i class="fa fa-th"></i> fa-th</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#th-large"><i class="fa fa-th-large"></i> fa-th-large</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#th-list"><i class="fa fa-th-list"></i> fa-th-list</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#underline"><i class="fa fa-underline"></i> fa-underline</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#undo"><i class="fa fa-undo"></i> fa-undo</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#chain-broken"><i class="fa fa-unlink"></i> fa-unlink <span class="text-muted">(alias)</span></a></div>

        </div>

    </section>

    <section id="directional">
        <h2 class="page-header">Ícones Direcionais</h2>

        <div class="row fontawesome-icon-list">



            <div class="fa-hover col-md-3 col-sm-4"><a href="#angle-double-down"><i class="fa fa-angle-double-down"></i> fa-angle-double-down</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#angle-double-left"><i class="fa fa-angle-double-left"></i> fa-angle-double-left</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#angle-double-right"><i class="fa fa-angle-double-right"></i> fa-angle-double-right</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#angle-double-up"><i class="fa fa-angle-double-up"></i> fa-angle-double-up</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#angle-down"><i class="fa fa-angle-down"></i> fa-angle-down</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#angle-left"><i class="fa fa-angle-left"></i> fa-angle-left</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#angle-right"><i class="fa fa-angle-right"></i> fa-angle-right</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#angle-up"><i class="fa fa-angle-up"></i> fa-angle-up</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#arrow-circle-down"><i class="fa fa-arrow-circle-down"></i> fa-arrow-circle-down</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#arrow-circle-left"><i class="fa fa-arrow-circle-left"></i> fa-arrow-circle-left</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#arrow-circle-o-down"><i class="fa fa-arrow-circle-o-down"></i> fa-arrow-circle-o-down</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#arrow-circle-o-left"><i class="fa fa-arrow-circle-o-left"></i> fa-arrow-circle-o-left</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#arrow-circle-o-right"><i class="fa fa-arrow-circle-o-right"></i> fa-arrow-circle-o-right</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#arrow-circle-o-up"><i class="fa fa-arrow-circle-o-up"></i> fa-arrow-circle-o-up</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#arrow-circle-right"><i class="fa fa-arrow-circle-right"></i> fa-arrow-circle-right</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#arrow-circle-up"><i class="fa fa-arrow-circle-up"></i> fa-arrow-circle-up</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#arrow-down"><i class="fa fa-arrow-down"></i> fa-arrow-down</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#arrow-left"><i class="fa fa-arrow-left"></i> fa-arrow-left</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#arrow-right"><i class="fa fa-arrow-right"></i> fa-arrow-right</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#arrow-up"><i class="fa fa-arrow-up"></i> fa-arrow-up</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#arrows"><i class="fa fa-arrows"></i> fa-arrows</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#arrows-alt"><i class="fa fa-arrows-alt"></i> fa-arrows-alt</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#arrows-h"><i class="fa fa-arrows-h"></i> fa-arrows-h</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#arrows-v"><i class="fa fa-arrows-v"></i> fa-arrows-v</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#caret-down"><i class="fa fa-caret-down"></i> fa-caret-down</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#caret-left"><i class="fa fa-caret-left"></i> fa-caret-left</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#caret-right"><i class="fa fa-caret-right"></i> fa-caret-right</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#caret-square-o-down"><i class="fa fa-caret-square-o-down"></i> fa-caret-square-o-down</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#caret-square-o-left"><i class="fa fa-caret-square-o-left"></i> fa-caret-square-o-left</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#caret-square-o-right"><i class="fa fa-caret-square-o-right"></i> fa-caret-square-o-right</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#caret-square-o-up"><i class="fa fa-caret-square-o-up"></i> fa-caret-square-o-up</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#caret-up"><i class="fa fa-caret-up"></i> fa-caret-up</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#chevron-circle-down"><i class="fa fa-chevron-circle-down"></i> fa-chevron-circle-down</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#chevron-circle-left"><i class="fa fa-chevron-circle-left"></i> fa-chevron-circle-left</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#chevron-circle-right"><i class="fa fa-chevron-circle-right"></i> fa-chevron-circle-right</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#chevron-circle-up"><i class="fa fa-chevron-circle-up"></i> fa-chevron-circle-up</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#chevron-down"><i class="fa fa-chevron-down"></i> fa-chevron-down</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#chevron-left"><i class="fa fa-chevron-left"></i> fa-chevron-left</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#chevron-right"><i class="fa fa-chevron-right"></i> fa-chevron-right</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#chevron-up"><i class="fa fa-chevron-up"></i> fa-chevron-up</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#hand-o-down"><i class="fa fa-hand-o-down"></i> fa-hand-o-down</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#hand-o-left"><i class="fa fa-hand-o-left"></i> fa-hand-o-left</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#hand-o-right"><i class="fa fa-hand-o-right"></i> fa-hand-o-right</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#hand-o-up"><i class="fa fa-hand-o-up"></i> fa-hand-o-up</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#long-arrow-down"><i class="fa fa-long-arrow-down"></i> fa-long-arrow-down</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#long-arrow-left"><i class="fa fa-long-arrow-left"></i> fa-long-arrow-left</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#long-arrow-right"><i class="fa fa-long-arrow-right"></i> fa-long-arrow-right</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#long-arrow-up"><i class="fa fa-long-arrow-up"></i> fa-long-arrow-up</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#caret-square-o-down"><i class="fa fa-toggle-down"></i> fa-toggle-down <span class="text-muted">(alias)</span></a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#caret-square-o-left"><i class="fa fa-toggle-left"></i> fa-toggle-left <span class="text-muted">(alias)</span></a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#caret-square-o-right"><i class="fa fa-toggle-right"></i> fa-toggle-right <span class="text-muted">(alias)</span></a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#caret-square-o-up"><i class="fa fa-toggle-up"></i> fa-toggle-up <span class="text-muted">(alias)</span></a></div>

        </div>

    </section>

    <section id="video-player">
        <h2 class="page-header">Ícones Vídeos</h2>

        <div class="row fontawesome-icon-list">



            <div class="fa-hover col-md-3 col-sm-4"><a href="#arrows-alt"><i class="fa fa-arrows-alt"></i> fa-arrows-alt</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#backward"><i class="fa fa-backward"></i> fa-backward</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#compress"><i class="fa fa-compress"></i> fa-compress</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#eject"><i class="fa fa-eject"></i> fa-eject</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#expand"><i class="fa fa-expand"></i> fa-expand</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#fast-backward"><i class="fa fa-fast-backward"></i> fa-fast-backward</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#fast-forward"><i class="fa fa-fast-forward"></i> fa-fast-forward</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#forward"><i class="fa fa-forward"></i> fa-forward</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#pause"><i class="fa fa-pause"></i> fa-pause</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#play"><i class="fa fa-play"></i> fa-play</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#play-circle"><i class="fa fa-play-circle"></i> fa-play-circle</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#play-circle-o"><i class="fa fa-play-circle-o"></i> fa-play-circle-o</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#step-backward"><i class="fa fa-step-backward"></i> fa-step-backward</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#step-forward"><i class="fa fa-step-forward"></i> fa-step-forward</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#stop"><i class="fa fa-stop"></i> fa-stop</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#youtube-play"><i class="fa fa-youtube-play"></i> fa-youtube-play</a></div>

        </div>

    </section>

    <section id="brand">
        <h2 class="page-header">Ícones Marcas</h2>

        <div class="alert alert-success">
            <ul class="margin-bottom-none padding-left-lg">
                <li>All brand icons are trademarks of their respective owners.</li>
                <li>The use of these trademarks does not indicate endorsement of the trademark holder by Font Awesome, nor vice versa.</li>
            </ul>

        </div>

        <div class="row fontawesome-icon-list">



            <div class="fa-hover col-md-3 col-sm-4"><a href="#adn"><i class="fa fa-adn"></i> fa-adn</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#android"><i class="fa fa-android"></i> fa-android</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#apple"><i class="fa fa-apple"></i> fa-apple</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#bitbucket"><i class="fa fa-bitbucket"></i> fa-bitbucket</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#bitbucket-square"><i class="fa fa-bitbucket-square"></i> fa-bitbucket-square</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#btc"><i class="fa fa-bitcoin"></i> fa-bitcoin <span class="text-muted">(alias)</span></a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#btc"><i class="fa fa-btc"></i> fa-btc</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#css3"><i class="fa fa-css3"></i> fa-css3</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#dribbble"><i class="fa fa-dribbble"></i> fa-dribbble</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#dropbox"><i class="fa fa-dropbox"></i> fa-dropbox</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#facebook"><i class="fa fa-facebook"></i> fa-facebook</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#facebook-square"><i class="fa fa-facebook-square"></i> fa-facebook-square</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#flickr"><i class="fa fa-flickr"></i> fa-flickr</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#foursquare"><i class="fa fa-foursquare"></i> fa-foursquare</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#github"><i class="fa fa-github"></i> fa-github</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#github-alt"><i class="fa fa-github-alt"></i> fa-github-alt</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#github-square"><i class="fa fa-github-square"></i> fa-github-square</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#gittip"><i class="fa fa-gittip"></i> fa-gittip</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#google-plus"><i class="fa fa-google-plus"></i> fa-google-plus</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#google-plus-square"><i class="fa fa-google-plus-square"></i> fa-google-plus-square</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#html5"><i class="fa fa-html5"></i> fa-html5</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#instagram"><i class="fa fa-instagram"></i> fa-instagram</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#linkedin"><i class="fa fa-linkedin"></i> fa-linkedin</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#linkedin-square"><i class="fa fa-linkedin-square"></i> fa-linkedin-square</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#linux"><i class="fa fa-linux"></i> fa-linux</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#maxcdn"><i class="fa fa-maxcdn"></i> fa-maxcdn</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#pagelines"><i class="fa fa-pagelines"></i> fa-pagelines</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#pinterest"><i class="fa fa-pinterest"></i> fa-pinterest</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#pinterest-square"><i class="fa fa-pinterest-square"></i> fa-pinterest-square</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#renren"><i class="fa fa-renren"></i> fa-renren</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#skype"><i class="fa fa-skype"></i> fa-skype</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#stack-exchange"><i class="fa fa-stack-exchange"></i> fa-stack-exchange</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#stack-overflow"><i class="fa fa-stack-overflow"></i> fa-stack-overflow</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#trello"><i class="fa fa-trello"></i> fa-trello</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#tumblr"><i class="fa fa-tumblr"></i> fa-tumblr</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#tumblr-square"><i class="fa fa-tumblr-square"></i> fa-tumblr-square</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#twitter"><i class="fa fa-twitter"></i> fa-twitter</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#twitter-square"><i class="fa fa-twitter-square"></i> fa-twitter-square</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#vimeo-square"><i class="fa fa-vimeo-square"></i> fa-vimeo-square</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#vk"><i class="fa fa-vk"></i> fa-vk</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#weibo"><i class="fa fa-weibo"></i> fa-weibo</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#windows"><i class="fa fa-windows"></i> fa-windows</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#xing"><i class="fa fa-xing"></i> fa-xing</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#xing-square"><i class="fa fa-xing-square"></i> fa-xing-square</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#youtube"><i class="fa fa-youtube"></i> fa-youtube</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#youtube-play"><i class="fa fa-youtube-play"></i> fa-youtube-play</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#youtube-square"><i class="fa fa-youtube-square"></i> fa-youtube-square</a></div>

        </div>
    </section>

    <section id="medical">
        <h2 class="page-header">Ícones médicos</h2>

        <div class="row fontawesome-icon-list">



            <div class="fa-hover col-md-3 col-sm-4"><a href="#ambulance"><i class="fa fa-ambulance"></i> fa-ambulance</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#h-square"><i class="fa fa-h-square"></i> fa-h-square</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#hospital-o"><i class="fa fa-hospital-o"></i> fa-hospital-o</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#medkit"><i class="fa fa-medkit"></i> fa-medkit</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#plus-square"><i class="fa fa-plus-square"></i> fa-plus-square</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#stethoscope"><i class="fa fa-stethoscope"></i> fa-stethoscope</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#user-md"><i class="fa fa-user-md"></i> fa-user-md</a></div>

            <div class="fa-hover col-md-3 col-sm-4"><a href="#wheelchair"><i class="fa fa-wheelchair"></i> fa-wheelchair</a></div>

        </div>

    </section>

    </div>

            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Fechar</button>
            </div>
        </div>
    </div>
</div>
<!-- modal -->
EOFPAGE;
    }

    /**
     * build tools for listing mode
     * @return string
     */
    protected function make_tools() {
        $return = '
                        <div class="clearfix">                       
                            <div class="btn-group pull-right">
                                <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">Ferramentas <i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right">';
        if ($this->loc_action['add'] !== false) {
            $return.= '<li><a title="Adicionar novo(a) ' . $this->msg['singular'] . '" href="' . URL . FILENAME . $this->loc_action['add'] . '">Add Novo</a></li>'
                    . '<li class="divider"></li>';
        }
        $return.='
                                    ' . $this->tools_call($this->ids_tools) . '
                                    <li class="divider"></li>
                                    <li><a data-toggle="modal" title="Lista de Ícones" href="#listIcons">Lista de Ícones</a></li>
                                </ul>
                            </div>
                        </div>   
              <br>';

        return $return;
    }

    /**
     * Make row HTML listing mode
     * @param array $Object
     * @return String
     */
    protected function MAKE_LISTING_MODE(array $Object) {
        return '<div class="row">
                    <div class="col-sm-12">
                        <section class="panel">
                            <header class="panel-heading">
                                ' . $this->msg['manager'] . ' ' . $this->msg['plural'] . '
                                <span class="tools pull-right">
                                    <a href="javascript:;" class="fa fa-chevron-down"></a>
                                    <a href="javascript:;" class="fa fa-times"></a>
                                </span>
                            </header>
                            <div class="panel-body">
                                <div class="adv-table">
                                ' . $Object['tools'] . '
                                    <table  class="display table table-bordered table-striped table-condensed checkboxs js-table-sortable" id="dynamic-table">
                                        ' . $Object['body_table'] . '
                                        <tbody>' . $Object['elements_table'] . '</tbody>
                                    </table>  
                                </div>
                            </div>
                        </section>';
    }

    /**
     * Check field is selected
     * @param mixed $default Current value
     * @param mixed $value Value needed to select
     * @return string
     */
    private function _check_selected($default, $value) {
        if ($default == $value) {
            return ' selected';
        }
    }

    /**
     * Buttons Form
     * @param string $main Name main buttom
     * @return String
     */
    private function Button_Form($main = 'Adicionar') {
        return <<<EOF
        <div class="form-group">
            <div class="col-lg-offset-3 col-lg-6">
                <button class="btn btn-primary" type="submit">{$main}</button>
                <button class="btn btn-default" type="reset">Cancelar</button>
            </div>
        </div>
EOF;
    }

    private function _check_checked($value) {
        switch ($value) {
            case 1:
                return ' checked';
                break;
            case $value !== NULL:
                return ' checked';
                break;
        }
    }

    /**
     * Left Elements Update mode
     * @param array $data
     * @return String
     */
    private function LEFT_ELEMENTS_Update($data) {
        $link = $data['link'] == '#' ? NULL : $data['link'];
        return <<<EOF
<div class="col-md-5">
        
            <div class="form-group">
                <label for="nome" class="control-label col-md-4">Estado</label>
                <div class="col-md-6">
                    <input name="status" type="checkbox"{$this->_check_checked($data['status'])} data-on-label="<i class='fa fa-check'></i>" data-off-label="<i class='fa fa-times'></i>">
                </div>
            </div>
        
            <div class="form-group">
                <label for="nome" class="control-label col-md-4">Nome<span id="field-required">*</span></label>
                <div class="col-md-6">
                    <input value="{$data['name']}" class="form-control" id="cname" name="name" minlength="2" type="text" required />
                </div>
            </div>
                    
            <div class="form-group">
                <label for="nome" class="control-label col-md-4">Posição<span id="field-required">*</span></label>
                <div class="col-md-6">
                   <div id="spinner1">
                        <div class="input-group input-small">
                            <input value="{$data['position']}" type="text" name="position" class="spinner-input form-control" maxlength="1" readonly>
                            <div class="spinner-buttons input-group-btn btn-group-vertical">
                                <button type="button" class="btn spinner-up btn-xs btn-default">
                                    <i class="fa fa-angle-up"></i>
                                </button>
                                <button type="button" class="btn spinner-down btn-xs btn-default">
                                    <i class="fa fa-angle-down"></i>
                                </button>
                            </div> 
                        </div>    
                    </div>    
                </div>
            </div>      

             <div class="form-group">
                <label for="nome" class="control-label col-md-4">Ícone<span id="field-required">*</span></label>
                <div class="col-md-6">
                    <input value="{$data['icone']}" class="form-control" name="icone" minlength="2" type="text" required />
                </div>
            </div>
            <div class="form-group">
                <label for="nome" class="control-label col-md-4">Link</label>
                <div class="col-md-6">
                    <input value="{$link}" class="form-control" name="link" type="text" />
                </div>
            </div>
            <div class="form-group">
                <label for="nome" class="control-label col-md-4">Submenu<span id="field-required">*</span></label>
                <div class="col-md-6">
                       <select id="e1" name="submenu" class="populate " style="width:100%">
                            <option value="1"{$this->_check_selected(1, $data['sub'])}>Ativado</option>
                            <option value="0"{$this->_check_selected(0, $data['sub'])}>Desativado</option>
                        </select>
                        <span class="help-block">Não mudar !</span>
                </div>
            </div>
                            
    
</div>
EOF;
    }

    /**
     * Full HTML for page Insert New
     * @param array $dados Query Result
     * @return string
     */
    protected function HTML_Update($dados) {
        $URL = URL . FILENAME . DS . Url::getURL($this->URL_ACTION) . DS . $dados['id'] . DS;
        return <<<EOF
                <div class="row">
                    <div class="col-lg-12">
                        <section class="panel">
                            <header class="panel-heading">Alterar {$this->msg['singular']}
                                <span class="tools pull-right">
                                    <a class="fa fa-chevron-down" href="javascript:;"></a>
                                    <a class="fa fa-times" href="javascript:;"></a>
                                </span>
                            </header>
                            <div class="panel-body">
                                <div class="form">
                                    <form class="cmxform form-horizontal" id="signupForm" method="post" enctype="multipart/form-data" action="{$URL}update">
                                        {$this->LEFT_ELEMENTS_Update($dados)}
                                        <div class="clearfix"></div>
                                        {$this->Button_Form('Alterar')}
                                    </form>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
EOF;
    }

}

/**
 * Class Required CSS and Javascript
 * @todo put in array the path files
 */
class requireds extends MenuConfig {

    /**
     * Load JS for page listing mode
     * @return string
     */
    private function JS_REQUIRED_LISTING() {
        return <<<EOF
<!-- Placed js at the end of the document so the pages load faster -->
<!--Core js-->
<script src="js/jquery.js"></script>
<script src="bs3/js/bootstrap.min.js"></script>
<script class="include" type="text/javascript" src="js/jquery.dcjqaccordion.2.7.js"></script>
<script src="js/jquery.scrollTo.min.js"></script>
<script src="js/jQuery-slimScroll-1.3.0/jquery.slimscroll.js"></script>
<script src="js/jquery.nicescroll.js"></script>
<!--Easy Pie Chart-->
<script src="js/easypiechart/jquery.easypiechart.js"></script>
<!--Sparkline Chart-->
<script src="js/sparkline/jquery.sparkline.js"></script>
<!--jQuery Flot Chart-->
<script src="js/flot-chart/jquery.flot.js"></script>
<script src="js/flot-chart/jquery.flot.tooltip.min.js"></script>
<script src="js/flot-chart/jquery.flot.resize.js"></script>
<script src="js/flot-chart/jquery.flot.pie.resize.js"></script>

<!--dynamic table-->
<script type="text/javascript" language="javascript" src="js/advanced-datatable/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="js/data-tables/DT_bootstrap.js"></script>
<!--common script init for all pages-->
<script src="js/scripts.js"></script>

<!--dynamic table initialization -->
<script src="js/dynamic_table_init.js"></script>
EOF;
    }

    /**
     * Load CSS for page listing mode
     * @return string
     */
    private function CSS_REQUIRED_LISTING() {
        return <<<EOF
<!--dynamic table-->
<link href="js/advanced-datatable/css/demo_page.css" rel="stylesheet" />
<link href="js/advanced-datatable/css/demo_table.css" rel="stylesheet" />
<link rel="stylesheet" href="js/data-tables/DT_bootstrap.css" />
<link href="css/glyphicons.css" rel="stylesheet" />
<link href="css/estilo.css" rel="stylesheet" />
EOF;
    }

    /**
     * Load all JS for page Update mode
     * @return string
     */
    private function JS_REQUIRED_UPDATE($data) {
        return <<<EOF
<!-- Placed js at the end of the document so the pages load faster -->
<!--Core js-->
<script src="js/jquery.js"></script>
<script src="js/jquery-1.8.3.min.js"></script>
<script src="bs3/js/bootstrap.min.js"></script>
<script src="js/jquery-ui-1.9.2.custom.min.js"></script>
<script class="include" type="text/javascript" src="js/jquery.dcjqaccordion.2.7.js"></script>
<script src="js/jquery.scrollTo.min.js"></script>
<script src="js/easypiechart/jquery.easypiechart.js"></script>
<script src="js/jQuery-slimScroll-1.3.0/jquery.slimscroll.js"></script>
<script src="js/jquery.nicescroll.js"></script>
<script src="js/jquery.nicescroll.js"></script>

<script src="js/bootstrap-switch.js"></script>

<script type="text/javascript" src="js/fuelux/js/spinner.min.js"></script>
<script type="text/javascript" src="js/bootstrap-fileupload/bootstrap-fileupload.js"></script>
<script type="text/javascript" src="js/bootstrap-wysihtml5/wysihtml5-0.3.0.js"></script>
<script type="text/javascript" src="js/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>

<script type="text/javascript" src="js/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="js/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="js/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="js/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="js/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>

<script type="text/javascript" src="js/jquery-multi-select/js/jquery.multi-select.js"></script>
<script type="text/javascript" src="js/jquery-multi-select/js/jquery.quicksearch.js"></script>

<script type="text/javascript" src="js/bootstrap-inputmask/bootstrap-inputmask.min.js"></script>

<script src="js/jquery-tags-input/jquery.tagsinput.js"></script>

<script src="js/select2/select2.js"></script>
<script src="js/select-init.js"></script>
<script type="text/javascript" src="js/jquery.validate.min.js"></script>

<!--common script init for all pages-->
<script src="js/scripts.js"></script>

<script src="js/toggle-init.js"></script>

<script src="js/advanced-form.js"></script>
<!--Easy Pie Chart-->
<script src="js/easypiechart/jquery.easypiechart.js"></script>
<!--Sparkline Chart-->
<script src="js/sparkline/jquery.sparkline.js"></script>
<!--jQuery Flot Chart-->
<script src="js/flot-chart/jquery.flot.js"></script>
<script src="js/flot-chart/jquery.flot.tooltip.min.js"></script>
<script src="js/flot-chart/jquery.flot.resize.js"></script>
<script src="js/flot-chart/jquery.flot.pie.resize.js"></script>   
<!--this page script-->
<script src="js/validation-init.js"></script>
EOF;
    }

    /**
     * Load CSS for page Update mode
     * @return string
     */
    private function CSS_REQUIRED_UPATE() {
        return <<<EOF
    <link rel="stylesheet" href="css/bootstrap-switch.css" />
    <link rel="stylesheet" type="text/css" href="js/bootstrap-fileupload/bootstrap-fileupload.css" />
    <link rel="stylesheet" type="text/css" href="js/bootstrap-wysihtml5/bootstrap-wysihtml5.css" />

    <link rel="stylesheet" type="text/css" href="js/bootstrap-datepicker/css/datepicker.css" />
    <link rel="stylesheet" type="text/css" href="js/bootstrap-timepicker/css/timepicker.css" />
    <link rel="stylesheet" type="text/css" href="js/bootstrap-colorpicker/css/colorpicker.css" />
    <link rel="stylesheet" type="text/css" href="js/bootstrap-daterangepicker/daterangepicker-bs3.css" />
    <link rel="stylesheet" type="text/css" href="js/bootstrap-datetimepicker/css/datetimepicker.css" />

    <link rel="stylesheet" type="text/css" href="js/jquery-multi-select/css/multi-select.css" />
    <link rel="stylesheet" type="text/css" href="js/jquery-tags-input/jquery.tagsinput.css" />

    <link rel="stylesheet" type="text/css" href="js/select2/select2.css" />
EOF;
    }

    protected function _LOAD_REQUIRED_UPDATE($data) {
        return $this->CSS_REQUIRED_UPATE() . $this->JS_REQUIRED_UPDATE($data);
    }

    /**
     * Load required files for page listing
     * @return Object
     */
    protected function _LOAD_REQUIRED_LISTING() {
        return $this->JS_REQUIRED_LISTING() . $this->CSS_REQUIRED_LISTING();
    }

}
