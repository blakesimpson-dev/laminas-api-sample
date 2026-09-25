# VHS tape for the README demo GIF.
# Record from the repo root with `composer serve` running and fresh fixtures:
#   composer fixtures:load && vhs docs/demo.tape
# Output: docs/demo.gif

Output docs/demo.gif

Set Shell bash
Set FontSize 14
Set LineHeight 1.2
Set Width 1100
Set Height 1060
Set Padding 20
Set Theme "Catppuccin Mocha"
Set TypingSpeed 30ms

# Hidden setup:
# - PS1 starts with a newline, so every command gets a blank line above it.
# - curl() wraps the real curl: when its output goes to the terminal it
#   pretty-prints the body with jq (coloured) and adds the HTTP status; when
#   it's piped (the list example) it passes the raw body through to your jq.
# - ID holds a fixture id for the update example.
Hide
Type `set +m; export PS1='\n$ '`
Enter
Type `curl() { if [ -t 1 ]; then command curl -s -o /tmp/demo-body -w '%{http_code}' -H 'Content-Type: application/json' "$@" > /tmp/demo-code; jq . /tmp/demo-body; read -r code < /tmp/demo-code; printf '\033[2mHTTP %s\033[0m\n' "$code"; else command curl -s "$@"; fi; }`
Enter
Type `ID=$(command curl -s localhost:8080/item-filter | jq -r '.filters[0].id')`
Enter
Ctrl+L
Show

# 1. List: the documented list shape omits the large filter body
Type `curl localhost:8080/item-filter | jq -c '.filters[] | {filter_name, type, public}'`
Enter
Sleep 2.5s

# 2. Create
Type `curl -X POST localhost:8080/item-filter -d '{"filter_name":"Demo","realm":"pc","filter":"Show"}'`
Enter
Sleep 3s

# 3. Validation error: 422, code 10
Type `curl -X POST localhost:8080/item-filter -d '{"filter_name":"Demo","realm":"mars","filter":"Show"}'`
Enter
Sleep 2.5s

# 4. Documented rule: a public filter can't be made private
Type `curl -X POST localhost:8080/item-filter/$ID -d '{"public":false}'`
Enter
Sleep 2.5s

# 5. Method map: 405, code 9
Type `curl -X DELETE localhost:8080/item-filter`
Enter
Sleep 3.5s
