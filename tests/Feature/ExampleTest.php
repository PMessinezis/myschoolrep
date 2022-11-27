<?php

test('home route returns 200', fn()=>$this->get(route('home'))->assertSuccessful());