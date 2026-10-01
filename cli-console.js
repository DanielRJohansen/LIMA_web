function GetHelp(line, cli) {
    const tokens = line.trim().split(/\s+/);
    if (tokens[0] !== 'lima') return cli.generalHelp;
    const name = tokens[1] === 'help' ? tokens[2] : tokens[1];
    return cli.commands.find(command => command.name === name)?.helpText || cli.generalHelp;
}

function InitializeConsole() {
    const data = document.getElementById('lima-cli-data');
    if (!data) return;
    const cli = JSON.parse(data.textContent);
    const output = document.getElementById('lima-console-output');
    const input = document.getElementById('lima-console-input');
    const status = document.getElementById('lima-console-status');
    const history = [];
    let historyIndex = 0;
    let draft = '';

    document.getElementById('lima-console-form').addEventListener('submit', event => {
        event.preventDefault();
        const line = input.value.trim();
        if (line) history.push(line);
        historyIndex = history.length;
        draft = '';
        output.textContent = '❯ ' + line + '\n\n' + GetHelp(line, cli);
        output.scrollTop = 0;
        status.textContent = 'Help displayed · ↑ history · Tab complete';
        input.value = '';
        input.focus();
    });

    input.addEventListener('keydown', event => {
        if (event.key === 'ArrowUp' || event.key === 'ArrowDown') {
            event.preventDefault();
            if (historyIndex === history.length) draft = input.value;
            historyIndex = Math.max(0, Math.min(history.length, historyIndex + (event.key === 'ArrowUp' ? -1 : 1)));
            input.value = historyIndex === history.length ? draft : history[historyIndex];
            input.setSelectionRange(input.value.length, input.value.length);
        }
        if (event.key === 'Tab' && !event.shiftKey) {
            const match = input.value.match(/^(lima\s+(?:help\s+)?)([^\s]*)$/);
            if (!match) return;
            const matches = cli.commands.filter(command => command.name.startsWith(match[2]));
            if (!matches.length) return;
            event.preventDefault();
            if (matches.length === 1) input.value = match[1] + matches[0].name + ' ';
            else status.textContent = matches.map(command => command.name).join(' · ');
        }
    });
}

if (typeof document !== 'undefined') InitializeConsole();
if (typeof module !== 'undefined') module.exports = { GetHelp };
