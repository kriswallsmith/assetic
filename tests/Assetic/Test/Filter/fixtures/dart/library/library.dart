import 'dart:html';

librayFunction() {
  var msg = query('#msg');
  var btn = new ButtonElement();
  var someThing = new Element.a();
  someThing.href = 'New Cool Link';

  btn.text = 'Click me!';
  btn.on.click.add((e) => msg.text = 'Dart!');
  document.body.nodes.add(btn);
}
