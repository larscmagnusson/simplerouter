# SimpleRouter
Just supersimple routing.. probably one of 10000 other super simple routing packages 

## Examples
```
GET /base?whaterver

SimpleRouter::setup("/base")->get('/', function () {
    return SimpleRouter::json(["hej", "å", "hå"]);
});
```

```
GET /base/sub?whaterver

SimpleRouter::setup("/base")->get('/sub', function () {
    return SimpleRouter::json(["hej", "å", "hå"]);
});
```

```
GET /base/sub/param1/param2

SimpleRouter::setup("/base")->get('/sub', function ($p1, $p2) {
    return SimpleRouter::json(["hej", "å", "hå"]);
});

```

```
GET /base/sub?what=ever

SimpleRouter::setup("/base")->get('/sub', function () {
    return SimpleRouter::json(["hej", "å", SimpleRouter::params('what')]);
});

```
```
GET /base

SimpleRouter::setup("/base")->get('/', function () {
    do_something_but_dont_return_anything();
});

```