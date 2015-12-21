<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<h1><a id="__PHP__0"></a>Тестовое задание PHP разработчика</h1>
<h2><a id="__1"></a>О проекте</h2>
<p><a href="https://github.com/novikovsergey/doublegis">Проект на GitHub (Там Readme выглядит лучше)</a></p>
<p>Постарался выполнить в соотвествии с заданием.</p>
<p>Возможно не учёл требования по производительности, понятие 1000000 пользователей размытое.
    Для проекта использовал Yii2, т.к. осваиваю его в рамках саморазвития. БД Postgres 9.4. Не использовал какого либо вида кеширование, в рамках тестого заданяи считаю избыточным. Тестировал на глаз.</p>
<p>Сейчас приложение крутится на домене <a href="http://badcoder.ru">badcoder.ru</a>, тачка домашняя Core 2 Duo 2.66, 6Gb ОЗУ, Debian 7</p>
<p>Админка badcoder.ru/admin</p>
<ul>
    <li>login: admin</li>
    <li>password: KZTgCD!–!-Epx</li>
    </ul>
<h2><a id="___9"></a>Требования к системе</h2>
<ul>
    <li>Apache2</li>
    <li>php 5.5</li>
    <li>Postgres 9.4</li>
    <li>Postgres Extension PostGIS</li>
    <li>Postgres Extension Gist</li>
    <li>composer</li>
    <li>git</li>
</ul>
<h2><a id="_17"></a>Установка</h2>
<pre><code class="language-sh">git <span class="hljs-built_in">clone</span> https://github.com/novikovsergey/doublegis.git
        composer install
    </code></pre>
<p>После настроить подключение к БД, затем:</p>
<pre><code class="language-sh"> ./yii migrate
        ./yii generator/generate
    </code></pre>
<p>Генератор тестовых данных на больших обьёмах работае очень медленно, т.к. я ограничился ActiveRecord, по хорошему надо передлать, на массовые вставки данных в БД.</p>
<h2><a id="_28"></a>Методы</h2>
<h1><a id="1_buildings_29"></a>1. buildings</h1>
<p>Метод выводит список всех зданий</p>
<h4><a id="_31"></a>Параметры</h4>
<p>не имеет</p>
<h4><a id="__33"></a>Описание ответа</h4>
<p>список обьектов здания</p>
<h4><a id="_36"></a>Поля:</h4>
<ul>
    <li>id - идентификатор БД</li>
    <li>address - строка с адресом</li>
    <li>location - координаты на плоскости в формате “x,y”</li>
</ul>
<h4><a id="__40"></a>Пример запроса</h4>
<ul>
    <li><a href="http://badcoder.ru/buildings">badcoder.ru/buildings</a></li>
</ul>
<h4><a id="__42"></a>Пример ответа</h4>
<pre><code class="language-json">{
        id: 1,
        address: "821295, Калужская область, город Клин, ул. Славы, 20",
        location: "-95.175643,82.44479"
        }
    </code></pre>
<h1><a id="2_rubrics_50"></a>2. rubrics</h1>
<p>Метод возвращает всех родителей для заданных потомков, если параметры не указаны дерево возвращается целиком</p>
<h4><a id="_52"></a>Параметры</h4>
<ul>
    <li>ids - Идентификаторы вершин перечисленные через запятую  для которых вернуть родителей, по умолчанию не задан</li>
</ul>
<h4><a id="__55"></a>Описание ответа</h4>
<p>список обьектов, произвольной вложенности</p>
<ul>
    <li>id - идентификатор</li>
    <li>title - строка название</li>
    <li>parent_id - идентификатор родителя</li>
    <li>subrubrics - массив вложенных обьектов</li>
</ul>
<h4><a id="__63"></a>Пример запроса</h4>
<ul>
    <li><a href="http://badcoder.ru/rubrics?ids=10,25">badcoder.ru/rubrics?ids=10,25</a></li>
</ul>
<h4><a id="__66"></a>Пример ответа</h4>
<pre><code class="language-json">{
        id: 10,
        title: "tenetur",
        parent_id: null,
        subrubrics: [ ]
        },
        {
        id: 11,
        title: "deleniti",
        parent_id: null,
        subrubrics: [
        {
        id: 20,
        title: "aut",
        parent_id: 11,
        subrubrics: [
        {
        id: 25,
        title: "tempora",
        parent_id: 20,
        subrubrics: [ ]
        }
        ]
        }
        ]
        }
    </code></pre>
<h1><a id="3_companies_95"></a>3. companies</h1>
<p>Метод возвращает список компаний подходящих запросу, ограничение вывода 1000 компаний</p>
<h4><a id="_97"></a>Параметры</h4>
<ul>
    <li>ids - Идентификаторы необходимых компаний перечисленные через запятую</li>
    <li>building_id - Идентификатор здания в котором находятся компании</li>
    <li>rubric_ids -  Идентификаторы  перечисленные через запятую рубрик к которым относятся компании, компании  выводятся для всех наследников указанных рубрик</li>
    <li>radius - Параметр описывает окружность в формате “x,y,R” в которую попадают компании</li>
    <li>envelope - Параметр описывает прямоугольную область в формать “x_min,y_min,x_max,y_max” в которую попадают компании.</li>
    <li>q - Строка поиска по названию компании</li>
</ul>
<blockquote>
    <p>Внимание параметры radius и envelope взаимоисключающие!</p>
</blockquote>
<h4><a id="__106"></a>Описание ответа</h4>
<p>список обьектов компаний</p>
<ul>
    <li>id - идентификатор БД</li>
    <li>title - строка название компании</li>
    <li>address - адресс здания в котором находится компания</li>
    <li>phone - массив с телефонами компании</li>
    <li>rubrics - массив обьектов рубрик</li>
</ul>
<h4><a id="__114"></a>Пример запроса</h4>
<ul>
    <li><a href="http://badcoder.ru/companies?ids=10,25">badcoder.ru/companies?ids=10,25</a></li>
    <li><a href="http://badcoder.ru/companies?rubric_ids=18">badcoder.ru/companies?rubric_ids=18</a></li>
    <li><a href="http://badcoder.ru/companies?radius=10,-5,34.5&amp;q=%D0%A2%D0%B5%D0%BA%D1%81%D1%82%D0%B8%D0%BB%D1%8C">badcoder.ru/companies?radius=10,-5,34.5&amp;q=Текстиль</a></li>
    <li><a href="http://badcoder.ru/companies?envelope=10,-5,14.5,13.768346&amp;rubric_ids=78,3">badcoder.ru/companies?envelope=10,-5,14.5,13.768346&amp;rubric_ids=78,3</a></li>
    <li><a href="http://badcoder.ru/companies?building_id=10&amp;q=%D0%B3%D0%B0%D1%80%D0%B0%D0%B6">badcoder.ru/companies?building_id=10&amp;q=гараж</a></li>
</ul>
<h4><a id="__121"></a>Пример ответа</h4>
<pre><code class="language-json">{
        id: 6199,
        title: "ЗАО ЦементТяжХоз",
        address: "582927, Амурская область, город Одинцово, бульвар Будапештсткая, 08",
        phones: [
        "(812) 810-49-02",
        "+7 (922) 574-9010",
        "(812) 535-86-79",
        "(35222) 69-2934",
        "8-800-840-8476"
        ],
        rubrics: [
        {
        id: 714,
        title: "Quae"
        },
        {
        id: 383,
        title: "Tempore"
        }
        ]
        },
    </code></pre>
<h2><a id="___147"></a>Коды ошибок методов</h2>
<ul>
    <li>200 - Успешный запроса</li>
    <li>400 - Забпрос составлен неверно</li>
</ul>
