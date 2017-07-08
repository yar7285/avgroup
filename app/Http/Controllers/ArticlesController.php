<?php

namespace App\Http\Controllers;

use App\Category;
use App\Repositories\ArticlesRepository;
use App\Repositories\CommentsRepository;
use App\Repositories\PortfoliosRepository;
use App\Menu;
use App\Repositories\MenusRepositories;
use Illuminate\Http\Request;

class ArticlesController extends SiteController
{
    public function __construct(PortfoliosRepository $p_rep, ArticlesRepository $a_rep, CommentsRepository $c_rep )
    {
        parent::__construct(new MenusRepositories(new Menu));

        $this->p_rep = $p_rep;
        $this->a_rep = $a_rep;
        $this->c_rep = $c_rep;

        $this->bar = 'right';
        $this->template = env('THEME').'.articles';
    }

    public function index($cat_alias = false) {

        $articles = $this->getArticles($cat_alias);
//        dd($articles);
        $content = view(env('THEME').'.articles_content')->with('articles',$articles)->render();
        $this->vars = array_add($this->vars,'content',$content);

        $comments = $this->getComments(config('settings.recent_comments'));
        $portfolios = $this->getPortfolios(config('settings.recent_portfolios'));
        $this->contentRightBar = view(env('THEME').'.articlesBar')-> with(['comments'=> $comments,'portfolios'=>$portfolios]);

        return $this->renderOutput();
    }

    public function getArticles($alias = false) {

        $where = false;
        if ($alias) {
            $id = Category::select('id')->where('alias',$alias)->first()->id;
//            dd($id);
            $where = ['category_id',$id];
        }

        $articles = $this->a_rep->get(['title','alias','created_at','img','desc','user_id','category_id','id'],false,true,$where);
//        dd($articles);
        if ($articles) {
            $articles->load('user','category','comments');
        }
        return $articles;
    }
    public function getComments($take) {

        $comments = $this->c_rep->get(['text','name','email','site','article_id','user_id'],$take);
//        dd($comments);
        if ($comments) {
            $comments->load('article','user');
        }
        return $comments;
    }
    public function getPortfolios($take) {

        $portfolios = $this->p_rep->get(['title','text','alias','customer','img','filter_alias'],$take);
//        dd($portfolios);
        return $portfolios;
    }

    public function show($alias = false) {

        $article = $this->a_rep->one($alias,['comments' => true]);
//        dd($article);
        if ($article) {
            $article->img = json_decode( $article->img);
        }


        $content = view(env('THEME').'.article_content')->with('article',$article)->render();
        $this->vars = array_add($this->vars,'content',$content);

//        dd($article);
        $comments = $this->getComments(config('settings.recent_comments'));
        $portfolios = $this->getPortfolios(config('settings.recent_portfolios'));
        $this->contentRightBar = view(env('THEME').'.articlesBar')-> with(['comments'=> $comments,'portfolios'=>$portfolios]);

        return $this->renderOutput();
    }
}
