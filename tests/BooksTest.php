<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class BooksTest extends TestCase
{
    /**
     * A test of creating book in storage
     *
     * @return void
     */
    public function testBookCreate()
    {
        $data = [
            'title' => 'Волшебник изумрудного города',
            'author' => 'Волков А.М.',
            'publication_year' => 2017,
            'description' => 'Сказочная повесть "Волшебник Изумрудного города" является переработкой сказки 
                американского писателя Ф. Баума, Она рассказывает об удивительных приключениях девочки Элли и ее 
                друзей в Волшебной стране'
        ];

        $this->put('/api/v1/book', $data)->seeJsonStructure(['success', 'book_id']);
    }

    /**
     * A test of updating book in database
     *
     * @return void
     */
    public function testBookUpdate()
    {
        $data = [
            'title' => 'Волшебник',
            'author' => 'Волков А.',
            'publication_year' => 2016,
            'description' => 'Сказочная повесть "Волшебник Изумрудного города" является переработкой сказки 
                американского писателя Ф. Баума'
        ];

        $this->post('/api/v1/book/2', $data)->seeJsonStructure(['success', 'book_id']);
    }

    /**
     * A test of updating book in database
     *
     * @return void
     */
    public function testBookDelete()
    {
        $this->delete('/api/v1/book/2')->seeJsonEquals(['success' => 'deleted']);
    }
}
