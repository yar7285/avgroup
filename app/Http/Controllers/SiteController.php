<?php

namespace App\Http\Controllers;


use App\Repositories\MenusRepositories;
use Illuminate\Http\Request;
use Menu;

class SiteController extends Controller
{
    protected $p_rep;
    protected $s_rep;
    protected $m_rep;
    protected $a_rep;
    protected $c_rep;

    protected $keywords;
    protected $meta_desc;
    protected $title;


    protected $template;


    protected $vars = [];

    protected $contentRightBar = false;
    protected $contentLeftBar = false;

    protected $bar = 'no';

    public function __construct(MenusRepositories $m_rep)
    {
        $this->m_rep=$m_rep;
    }
    protected function renderOutput() {

        $menu = $this->getMenu();
//        dd($menu);

        if($this->contentRightBar) {
            $rightBar = view(env('THEME').'.rightBar')->with('content_rightBar',$this->contentRightBar)->render();
            $this->vars = array_add($this->vars,'rightBar',$rightBar);
        }

        $this->vars = array_add($this->vars,'bar',$this->bar);

        $navigation = view(env('THEME').'.navigation')->with('menu',$menu)->render();
        $this->vars = array_add($this->vars,'navigation',$navigation);

        $footer = view(env('THEME').'.footer')->render();
        $this->vars = array_add($this->vars,'footer', $footer);

        $this->vars = array_add($this->vars,'keywords', $this->keywords);
        $this->vars = array_add($this->vars,'meta_desc', $this->meta_desc);
        $this->vars = array_add($this->vars,'title', $this->title);

        return view($this->template)->with($this->vars);
//        dd($menu);
    }
    protected function getMenu() {
        $menu = $this->m_rep->get();
//        dd($menu);
        $mBuilder = Menu::make('MyNav', function ($m) use ($menu) {
            foreach ($menu as $item) {

                if($item->parent == 0) {
                    $m->add($item->title,$item->path)->id($item->id);
                } else {
                    if ($m->find($item->parent)) {
                        $m->find($item->parent)->add($item->title,$item->path)->id($item->id);
                    }
                    if($m->find($item->sub_parent)) {
                        $m->find($item->sub_parent)->add($item->title,$item->path)->id($item->id);
                    }
                }
            }
        });
//        dd($mBuilder);
        return $mBuilder;
    }
}
