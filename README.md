# MicroPHP Framework

[![Author](http://img.shields.io/badge/author-@microcmf-blue.svg?style=flat-square)](https://twitter.com/microcmf)
[![Join the chat at https://gitter.im/linpax/microphp-framework](https://badges.gitter.im/linpax/microphp-framework.svg)](https://gitter.im/linpax/microphp-framework?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![Code Climate](https://codeclimate.com/github/linpax/microphp-framework/badges/gpa.svg)](https://codeclimate.com/github/linpax/microphp-framework)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/linpax/microphp-framework/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/linpax/microphp-framework/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/linpax/microphp-framework/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/linpax/microphp-framework/?branch=master)
[![Build Status](https://travis-ci.org/linpax/microphp-framework.svg?branch=master)](https://travis-ci.org/linpax/microphp-framework)
[![HHVM Status](http://hhvm.h4cc.de/badge/linpax/microphp-framework.svg)](http://hhvm.h4cc.de/package/linpax/microphp-framework)
[![Latest Stable Version](https://poser.pugx.org/linpax/microphp-framework/v/stable)](https://packagist.org/packages/linpax/microphp-framework)
[![Total Downloads](https://poser.pugx.org/linpax/microphp-framework/downloads)](https://packagist.org/packages/linpax/microphp-framework)
[![Reference Status](https://www.versioneye.com/php/linpax:microphp-framework/reference_badge.svg?style=flat)](https://www.versioneye.com/php/linpax:microphp-framework/references)
[![Latest Unstable Version](https://poser.pugx.org/linpax/microphp-framework/v/unstable)](https://packagist.org/packages/linpax/microphp-framework)
[![License](https://poser.pugx.org/linpax/microphp-framework/license)](https://packagist.org/packages/linpax/microphp-framework)
[![composer.lock](https://poser.pugx.org/linpax/microphp-framework/composerlock)](https://packagist.org/packages/linpax/microphp-framework)

Micro — молодой H-MVC [фреймворк](http://wiki.micro.linpax.org/Фреймворк) со свободным исходным кодом, написанный на языке программирования [PHP](http://wiki.micro.linpax.org/PHP), для разработки полноценных веб-сервисов и приложений.
Micro реализует [паттерн](http://wiki.micro.linpax.org/Шаблон проектирования) «иерархический [модель](http://wiki.micro.linpax.org/Модель)-[представление](http://wiki.micro.linpax.org/Представление)-[контроллер](http://wiki.micro.linpax.org/Контроллер)» (HMVC).
Текущая стабильная версия отсутствует, распространяется по свободной [лицензией MIT](http://wiki.micro.linpax.org/Лицензия MIT).


## История
Работа по созданию Micro началась 28 декабря 2013 года, главным аспектом которого было желание получить мощный инструмент для ускорения разработки веб-сервисов и приложений, затратив небольшое количество ресурсов.

## Особенности

* [Прост](http://wiki.micro.linpax.org/Вводная) в понимании
* Основан на [PHP](http://wiki.micro.linpax.org/PHP) версии >= 5.4
* Использует парадигму [HMVC](http://wiki.micro.linpax.org/HMVC)
* Диспетчер URL с использованием [Router'а](http://wiki.micro.linpax.org/Router) путей
* Очень легко расширяем
* Малый размер дистрибутива ( ~400 Kb )

## Возможности

* Многофункциональная [настройка приложений](http://wiki.micro.linpax.org/конфигурация)
* Поддержка [баз данных](http://wiki.micro.linpax.org/База данных) (реализована через драйвер [PDO](http://wiki.micro.linpax.org/PHP Data Objects))
* Поддержка [ActiveRecord](http://wiki.micro.linpax.org/ActiveRecord) для работы с данными
* Поддержка URL любой сложности
* [Легко расширяемый](http://wiki.micro.linpax.org/Конфигурация) базовый функционал
* Минимальный [джентльменский набор](http://api.micro.linpax.org/namespace-Micro.html) для повседневных операций
* Встроенный механизм поддержки миграций
* Поддержка интернационализации
* Возможность подключения сторонних библиотек
* Генераторы HTML-кода, форм, а также [виджеты](http://wiki.micro.linpax.org/Виджет)
* Удобный [построитель запросов](http://wiki.micro.linpax.org/Query)
* Низкий порог вхождения
