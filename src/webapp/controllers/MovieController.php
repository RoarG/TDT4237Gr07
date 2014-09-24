<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\models\Movie;
use tdt4237\webapp\models\MovieReview;
use tdt4237\webapp\Auth;

class MovieController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $movies = Movie::all();

        usort($movies, function ($a, $b) {
            return strcmp($a->getName(), $b->getName());
        });

        $this->render('movies.twig', ['movies' => $movies]);
    }

    /**
     * Show movie by id.
     */
    function show($id)
    {
        $this->render('showmovie.twig', [
            'movie' => Movie::find($id),
            'reviews' => MovieReview::findByMovieId($id)
        ]);
    }

    function addReview($id)
    {
        $author = $this->app->request->post('author');
        $text = $this->app->request->post('text');

        $review = MovieReview::makeEmpty();
        $review->setAuthor($author);
        $review->setText($text);
        $review->setMovieId($id);

        $review->save();

        $this->app->flash('info', 'The review was successfully saved.');
        $this->app->redirect('/movies/' . $id);
    }
}
