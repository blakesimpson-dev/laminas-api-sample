
Output docs/demo.gif

Set Shell bash
Set FontSize 14
Set LineHeight 1.2
Set Width 1100
Set Height 400
Set Padding 20
Set Theme "Dracula"
Set TypingSpeed 20ms

Hide
Type `set +m; export PS1='\n$ '; rm -f /tmp/demo-body /tmp/demo-code`
Enter
Type `curl() { if [ -t 1 ]; then command curl -s -o /tmp/demo-body -w '%{http_code}' -H 'Content-Type: application/json' "$@" > /tmp/demo-code; jq . /tmp/demo-body; read -r code < /tmp/demo-code; printf '\033[2mHTTP %s\033[0m\n' "$code"; else command curl -s "$@"; fi; }`
Enter
Type `ID=$(command curl -s localhost:8080/item-filter | jq -r '.filters[0].id')`
Enter
Sleep 1s
Type `clear`
Enter
Sleep 500ms
Show

Type `curl localhost:8080/item-filter | jq '.filters |= [.[0], "..."]'`
Enter
Sleep 3s

Ctrl+L
Type `curl -X POST localhost:8080/item-filter -d '{"filter_name":"Demo","realm":"pc","filter":"Show"}'`
Enter
Sleep 3.5s

Ctrl+L
Type `curl -X POST localhost:8080/item-filter -d '{"filter_name":"Demo","realm":"mars","filter":"Show"}'`
Enter
Sleep 3s

Ctrl+L
Type `curl -X POST localhost:8080/item-filter/$ID -d '{"public":false}'`
Enter
Sleep 3s

Ctrl+L
Type `curl -X DELETE localhost:8080/item-filter`
Enter
Sleep 3.5s
