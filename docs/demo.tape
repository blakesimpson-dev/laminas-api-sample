
Output docs/demo.gif

Set Shell bash
Set FontSize 14
Set LineHeight 1.2
Set Width 1100
Set Height 1060
Set Padding 20
Set Theme "Catppuccin Mocha"
Set TypingSpeed 30ms

Hide
Type `set +m; export PS1='\n$ '`
Enter
Type `curl() { if [ -t 1 ]; then command curl -s -o /tmp/demo-body -w '%{http_code}' -H 'Content-Type: application/json' "$@" > /tmp/demo-code; jq . /tmp/demo-body; read -r code < /tmp/demo-code; printf '\033[2mHTTP %s\033[0m\n' "$code"; else command curl -s "$@"; fi; }`
Enter
Type `ID=$(command curl -s localhost:8080/item-filter | jq -r '.filters[0].id')`
Enter
Ctrl+L
Show

Type `curl localhost:8080/item-filter | jq -c '.filters[] | {filter_name, type, public}'`
Enter
Sleep 2.5s

Type `curl -X POST localhost:8080/item-filter -d '{"filter_name":"Demo","realm":"pc","filter":"Show"}'`
Enter
Sleep 3s

Type `curl -X POST localhost:8080/item-filter -d '{"filter_name":"Demo","realm":"mars","filter":"Show"}'`
Enter
Sleep 2.5s

Type `curl -X POST localhost:8080/item-filter/$ID -d '{"public":false}'`
Enter
Sleep 2.5s

Type `curl -X DELETE localhost:8080/item-filter`
Enter
Sleep 3.5s
