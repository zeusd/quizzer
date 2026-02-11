const params = new URLSearchParams(window.location.search);
const uid = params.get('z');

if (!uid) {
    document.getElementById('results-container').textContent =
        'No attempt specified.';
    throw new Error('Missing uid');
}

function renderResults(data) {
    const container = document.getElementById('results-container');
    var pcnt = 0;
    if (data.gt == data.tot || data.lt == 0){
        pcnt = 99.99;
    } else if (data.gt == 0){
        pcnt = 0.01;
    } else {
        pcnt = (data.gt / data.tot) * 100;
    }
    const summary = document.createElement('p');
    summary.textContent = `Score: ${data.score} / ${data.max}. You did better than ${data.lt} people and worse than ${data.gt}, out of ${data.tot} test-takers. You are in the top ${pcnt.toFixed(2)}% of participants.`;
    container.appendChild(summary);

    // TODO: backend
    // data.questions.forEach((q, i) => {
    //     const block = document.createElement('div');
    //     block.className = 'result-question';
    //
    //     const title = document.createElement('h3');
    //     title.textContent = `${i + 1}. ${q.question}`;
    //     block.appendChild(title);
    //
    //     Object.entries(q.answers).forEach(([key, text]) => {
    //         const p = document.createElement('p');
    //         p.textContent = text;
    //
    //         if (key === q.correct) p.classList.add('correct');
    //         if (key === q.user && key !== q.correct) p.classList.add('wrong');
    //
    //         block.appendChild(p);
    //     });
    //
    //     container.appendChild(block);
    // });
}

fetch(`../scripts/results.php?z=${uid}`)
    .then(res => res.json())
    .then(data => {
        renderResults(data);
    });
