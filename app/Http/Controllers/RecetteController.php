<?php

namespace App\Http\Controllers;
 
use App\DataTables\RecetteDataTable;
 
class RecetteController extends Controller
{
    public function index(RecetteDataTable $dataTable)
    {
        // dd($dataTable);
        return $dataTable->render('recette.index');
    }
    public function create(){
        // dd('aa');
        return view('recette.create');
    }
}