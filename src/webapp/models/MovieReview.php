<?php

namespace tdt4237\webapp\models;

class MovieReview
{
    private $id = null;
    private $movieId;
    private $author;
    private $text;

    static $app;

    public function getId()
    {
        return $this->id;
    }

    public function getMovieId()
    {
        return $this->movieId;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setMovieId($id)
    {
        $this->movieId = $id;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    static function make($id, $author, $text)
    {
        $review = new MovieReview();
        $review->id = $id;
        $review->author = $author;
        $review->text = $text;

        return $review;
    }

    /**
     * Insert or save review into db.
     */
    function save()
    {
        if ($this->id === null) {
            $stmt = self::$app->db->prepare("INSERT INTO moviereviews (movieid, author, text) VALUES (?, ?, ?)");
        } else {
            // TODO: Update moviereview here
        }

        return $stmt->execute(array($this->movieId, $this->author, $this->text));
    }

    static function makeEmpty()
    {
        return new MovieReview();
    }

    /**
     * Fetch all movie reviews by movie id.
     */
    static function findByMovieId($id)
    {
        $stmt = self::$app->db->prepare("SELECT * FROM moviereviews WHERE movieid = ?");
        $stmt->execute(array($id));
        $results = $stmt->fetchAll();

        $reviews = [];

        foreach ($results as $row) {
            $review = self::makeFromRow($row);
            array_push($reviews, $review);
        }

        return $reviews;
    }

    static function makeFromRow($row) {
        $review = self::make(
            $row['id'],
            $row['author'],
            $row['text']
        );

        return $review;
    }
}
MovieReview::$app = \Slim\Slim::getInstance();
