const assert = require('node:assert/strict');
const test = require('node:test');
const fs = require('node:fs');
const vm = require('node:vm');
const cli = require('../public/resources/cli-help.json');
const { GetHelp } = require('../public/cli-console.js');

test('all commands return exact help regardless of options', () => {
    assert.equal(cli.commands.length, 12);
    for (const command of cli.commands) {
        for (const line of [`lima ${command.name}`, `lima ${command.name} --help`, `lima help ${command.name}`, `lima ${command.name} --arbitrary argument`]) {
            assert.equal(GetHelp(line, cli), command.helpText, line);
        }
    }
});

test('global help and unknown input fall back to general help', () => {
    for (const line of ['', 'lima', 'lima --help', 'lima -h', 'lima -help', 'lima help', 'lima --version', 'lima nope', 'lima help nope', 'whoami', 'lima MDRUN', '<script>alert(1)</script>']) {
        assert.equal(GetHelp(line, cli), cli.generalHelp, line);
    }
});

test('submission, history, draft restoration, completion and keyboard exit', () => {
    const elements = new Map();
    for (const id of ['lima-cli-data', 'lima-console-output', 'lima-console-input', 'lima-console-status', 'lima-console-form']) {
        elements.set(id, { textContent: '', value: '', listeners: {}, addEventListener(name, callback) { this.listeners[name] = callback; }, focus() {}, setSelectionRange() {} });
    }
    elements.get('lima-cli-data').textContent = JSON.stringify(cli);
    const context = vm.createContext({ document: { getElementById: id => elements.get(id) } });
    vm.runInContext(fs.readFileSync(require.resolve('../public/cli-console.js'), 'utf8'), context);
    const input = elements.get('lima-console-input');
    const output = elements.get('lima-console-output');
    const form = elements.get('lima-console-form');
    function Press(key, shiftKey = false) {
        let prevented = false;
        input.listeners.keydown({ key, shiftKey, preventDefault() { prevented = true; } });
        return prevented;
    }
    input.value = 'lima mdrun';
    form.listeners.submit({ preventDefault() {} });
    assert.equal(output.textContent, '❯ lima mdrun\n\n' + cli.commands[0].helpText);
    assert.equal(input.value, '');
    input.value = 'unfinished draft';
    Press('ArrowUp');
    assert.equal(input.value, 'lima mdrun');
    Press('ArrowDown');
    assert.equal(input.value, 'unfinished draft');
    input.value = 'lima build';
    assert.equal(Press('Tab'), true);
    assert.equal(input.value, 'lima buildmembrane ');
    assert.equal(Press('Tab', true), false);
    input.value = 'unknown';
    assert.equal(Press('Tab'), false);
    form.listeners.submit({ preventDefault() {} });
    assert.equal(output.textContent, '❯ unknown\n\n' + cli.generalHelp);
});
