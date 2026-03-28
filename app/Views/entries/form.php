<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-journal-plus"></i> <?= e(__('entry.title')) ?>
            </div>
            <div class="card-body entry-form">
                <!-- Smart Entry Voice Input -->
                <div id="smart-entry-bar" class="mb-3">
                    <button type="button" id="smart-entry-btn" class="btn btn-outline-primary w-100">
                        <i class="bi bi-mic"></i> <span id="smart-entry-label"><?= e(__('entry.smart_entry')) ?></span>
                    </button>
                    <div id="smart-entry-status" class="text-center small mt-2 d-none">
                        <span id="smart-entry-indicator" class="text-danger"><i class="bi bi-record-circle"></i> <?= e(__('entry.listening')) ?></span>
                        <div id="smart-entry-transcript" class="text-muted fst-italic mt-1"></div>
                    </div>
                </div>

                <form method="POST" action="<?= $entry ? '/entry/' . $entry['id'] : '/entry' ?>" id="entry-form">
                    <?= csrf_field() ?>

                    <div class="row g-3">
                        <!-- Date -->
                        <div class="col-md-4">
                            <label for="entry_date" class="form-label"><?= e(__('entry.date')) ?></label>
                            <input type="date" class="form-control" id="entry_date" name="entry_date"
                                   value="<?= e($entry['entry_date'] ?? $date) ?>"
                                   max="<?= date('Y-m-d') ?>" required>
                        </div>

                        <!-- Weight -->
                        <div class="col-md-4">
                            <label for="weight" class="form-label"><?= e(isMetric() ? __('entry.weight_metric') : __('entry.weight')) ?></label>
                            <?php $weightVal = ($entry['weight'] ?? '') !== '' ? (isMetric() ? round($entry['weight'] * 0.453592, 1) : $entry['weight']) : ''; ?>
                            <input type="number" step="0.1" class="form-control" id="weight" name="weight"
                                   value="<?= e($weightVal) ?>" placeholder="<?= isMetric() ? 'e.g. 84.0' : 'e.g. 185.5' ?>">
                        </div>

                        <!-- Calories -->
                        <div class="col-md-4">
                            <label for="calories" class="form-label"><?= e(__('entry.calories')) ?></label>
                            <input type="number" class="form-control" id="calories" name="calories"
                                   value="<?= e($entry['calories'] ?? '') ?>" placeholder="e.g. 2000">
                        </div>

                        <!-- Macros -->
                        <div class="col-md-4">
                            <label for="protein_g" class="form-label"><?= e(__('entry.protein')) ?></label>
                            <input type="number" step="0.1" class="form-control" id="protein_g" name="protein_g"
                                   value="<?= e($entry['protein_g'] ?? '') ?>" placeholder="e.g. 120">
                        </div>
                        <div class="col-md-4">
                            <label for="carbs_g" class="form-label"><?= e(__('entry.carbs')) ?></label>
                            <input type="number" step="0.1" class="form-control" id="carbs_g" name="carbs_g"
                                   value="<?= e($entry['carbs_g'] ?? '') ?>" placeholder="e.g. 200">
                        </div>
                        <div class="col-md-4">
                            <label for="fat_g" class="form-label"><?= e(__('entry.fat')) ?></label>
                            <input type="number" step="0.1" class="form-control" id="fat_g" name="fat_g"
                                   value="<?= e($entry['fat_g'] ?? '') ?>" placeholder="e.g. 65">
                        </div>

                        <!-- Vitals -->
                        <div class="col-md-6">
                            <label for="heart_rate" class="form-label"><?= e(__('entry.heart_rate')) ?></label>
                            <input type="number" class="form-control" id="heart_rate" name="heart_rate"
                                   value="<?= e($entry['heart_rate'] ?? '') ?>" placeholder="e.g. 72">
                        </div>
                        <div class="col-md-6">
                            <label for="blood_sugar" class="form-label"><?= e(isMetric() ? __('entry.blood_sugar_metric') : __('entry.blood_sugar')) ?></label>
                            <?php $bsVal = ($entry['blood_sugar'] ?? '') !== '' ? (isMetric() ? round($entry['blood_sugar'] * 0.0555, 1) : $entry['blood_sugar']) : ''; ?>
                            <input type="number" step="0.1" class="form-control" id="blood_sugar" name="blood_sugar"
                                   value="<?= e($bsVal) ?>" placeholder="<?= isMetric() ? 'e.g. 5.3' : 'e.g. 95.0' ?>">
                        </div>

                        <!-- Exercise -->
                        <div class="col-md-6">
                            <label for="exercise_type" class="form-label"><?= e(__('entry.exercise_type')) ?></label>
                            <select class="form-select" id="exercise_type" name="exercise_type">
                                <option value="">-- Select --</option>
                                <?php
                                $types = ['Walking', 'Running', 'Cycling', 'Swimming', 'Weight Training', 'Yoga', 'HIIT', 'Other'];
                                foreach ($types as $type):
                                ?>
                                <option value="<?= e($type) ?>" <?= ($entry['exercise_type'] ?? '') === $type ? 'selected' : '' ?>>
                                    <?= e($type) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="exercise_minutes" class="form-label"><?= e(__('entry.exercise_minutes')) ?></label>
                            <input type="number" class="form-control" id="exercise_minutes" name="exercise_minutes"
                                   value="<?= e($entry['exercise_minutes'] ?? '') ?>" placeholder="e.g. 30">
                        </div>

                        <!-- Notes -->
                        <div class="col-12">
                            <label for="notes" class="form-label"><?= e(__('entry.notes')) ?></label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"
                                      placeholder="Optional notes..."><?= e($entry['notes'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> <?= e(__('save')) ?>
                        </button>
                        <a href="/dashboard" class="btn btn-secondary"><?= e(__('cancel')) ?></a>
                        <?php if ($entry): ?>
                        <form method="POST" action="/entry/<?= $entry['id'] ?>/delete" class="ms-auto"
                              onsubmit="return confirm('Are you sure?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-trash"></i> <?= e(__('delete')) ?>
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    var btn = document.getElementById('smart-entry-btn');
    var status = document.getElementById('smart-entry-status');
    var transcript = document.getElementById('smart-entry-transcript');
    var label = document.getElementById('smart-entry-label');
    var form = document.getElementById('entry-form');

    if (!SpeechRecognition) {
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-mic-mute"></i> <?= e(__('entry.voice_not_supported')) ?>';
        return;
    }

    var recognition = new SpeechRecognition();
    recognition.continuous = true;
    recognition.interimResults = true;
    recognition.lang = '<?= e(\Core\Session::get('lang', 'en')) === 'es' ? 'es-US' : 'en-US' ?>';

    var isListening = false;
    var fullTranscript = '';

    // Field keyword mappings
    var fieldMap = [
        { keywords: ['weight', 'peso', 'weigh'], field: 'weight' },
        { keywords: ['calories', 'calorie', 'calorías', 'calorias', 'cal'], field: 'calories' },
        { keywords: ['protein', 'proteína', 'proteina'], field: 'protein_g' },
        { keywords: ['carbs', 'carb', 'carbohydrates', 'carbohidratos'], field: 'carbs_g' },
        { keywords: ['fat', 'fats', 'grasa'], field: 'fat_g' },
        { keywords: ['heart rate', 'heart', 'pulse', 'pulso', 'frecuencia'], field: 'heart_rate' },
        { keywords: ['blood sugar', 'sugar', 'glucose', 'azúcar', 'azucar', 'glucosa'], field: 'blood_sugar' },
        { keywords: ['exercise', 'minutes', 'ejercicio', 'minutos'], field: 'exercise_minutes' }
    ];

    // Exercise type keywords
    var exerciseTypes = {
        'walking': 'Walking', 'walk': 'Walking', 'walked': 'Walking', 'caminar': 'Walking',
        'running': 'Running', 'run': 'Running', 'ran': 'Running', 'correr': 'Running',
        'cycling': 'Cycling', 'bike': 'Cycling', 'biking': 'Cycling', 'ciclismo': 'Cycling',
        'swimming': 'Swimming', 'swim': 'Swimming', 'swam': 'Swimming', 'nadar': 'Swimming',
        'weight training': 'Weight Training', 'weights': 'Weight Training', 'lifting': 'Weight Training', 'pesas': 'Weight Training',
        'yoga': 'Yoga',
        'hiit': 'HIIT', 'hit': 'HIIT', 'interval': 'HIIT'
    };

    // Word-to-number map
    var wordNumbers = {
        'zero': 0, 'one': 1, 'two': 2, 'three': 3, 'four': 4, 'five': 5,
        'six': 6, 'seven': 7, 'eight': 8, 'nine': 9, 'ten': 10,
        'eleven': 11, 'twelve': 12, 'thirteen': 13, 'fourteen': 14, 'fifteen': 15,
        'sixteen': 16, 'seventeen': 17, 'eighteen': 18, 'nineteen': 19, 'twenty': 20,
        'thirty': 30, 'forty': 40, 'fifty': 50, 'sixty': 60, 'seventy': 70,
        'eighty': 80, 'ninety': 90, 'hundred': 100, 'thousand': 1000
    };

    function convertWordNumbers(text) {
        var words = text.toLowerCase().split(/\s+/);
        var result = [];
        var numAccum = 0;
        var hasNum = false;

        for (var i = 0; i < words.length; i++) {
            var w = words[i];
            if (wordNumbers[w] !== undefined) {
                var val = wordNumbers[w];
                if (val === 100) { numAccum = (numAccum || 1) * 100; }
                else if (val === 1000) { numAccum = (numAccum || 1) * 1000; }
                else { numAccum += val; }
                hasNum = true;
            } else {
                if (hasNum) { result.push(String(numAccum)); numAccum = 0; hasNum = false; }
                result.push(w);
            }
        }
        if (hasNum) result.push(String(numAccum));
        return result.join(' ');
    }

    function parseAndFill(text) {
        text = convertWordNumbers(text);
        var lower = text.toLowerCase();
        var filled = [];

        // Check for exercise type
        for (var word in exerciseTypes) {
            if (lower.indexOf(word) !== -1) {
                var sel = document.getElementById('exercise_type');
                if (sel) { sel.value = exerciseTypes[word]; filled.push('exercise_type → ' + exerciseTypes[word]); }
                break;
            }
        }

        // Parse field values
        for (var i = 0; i < fieldMap.length; i++) {
            var fm = fieldMap[i];
            for (var k = 0; k < fm.keywords.length; k++) {
                var kw = fm.keywords[k];
                var regex = new RegExp(kw + '(?:\\s+(?:is|of|in|at|was|lbs|lb|kg|pounds|kilos|grams|g|mg|dl|mg\\/dl|mmol|mmol\\/l|bpm|minutes|mins|min|[a-z\\/()]+))*\\s+([\\d]+\\.?[\\d]*)', 'i');
                var match = lower.match(regex);
                if (match) {
                    var input = document.getElementById(fm.field);
                    if (input) {
                        input.value = match[1];
                        input.classList.add('border-success');
                        setTimeout(function(el) { el.classList.remove('border-success'); }, 3000, input);
                        filled.push(fm.field + ' → ' + match[1]);
                    }
                    break;
                }
            }
        }

        return filled;
    }

    function checkSubmitCommand(text) {
        var lower = text.toLowerCase();
        var cmds = ['submit', 'save this', 'save it', 'save entry', 'guardar', 'enviar'];
        for (var i = 0; i < cmds.length; i++) {
            if (lower.indexOf(cmds[i]) !== -1) return true;
        }
        return false;
    }

    btn.addEventListener('click', function() {
        if (isListening) {
            recognition.stop();
            return;
        }
        isListening = true;
        fullTranscript = '';
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-danger');
        label.textContent = '<?= e(__('entry.stop_listening')) ?>';
        status.classList.remove('d-none');
        transcript.textContent = '';
        recognition.start();
    });

    recognition.onresult = function(event) {
        var interimText = '';
        var finalText = '';
        for (var i = event.resultIndex; i < event.results.length; i++) {
            var t = event.results[i][0].transcript;
            if (event.results[i].isFinal) {
                finalText += t + ' ';
            } else {
                interimText += t;
            }
        }

        if (finalText) {
            fullTranscript += finalText;
            var filled = parseAndFill(finalText);
            if (filled.length > 0) {
                transcript.textContent = filled.join(', ');
            }

            if (checkSubmitCommand(finalText)) {
                recognition.stop();
                setTimeout(function() { form.submit(); }, 500);
                return;
            }
        }

        if (interimText) {
            transcript.textContent = interimText;
        }
    };

    recognition.onerror = function(event) {
        console.error('[SmartEntry] Error:', event.error);
        resetBtn();
        if (event.error === 'not-allowed') {
            transcript.textContent = '<?= e(__('entry.mic_denied')) ?>';
            status.classList.remove('d-none');
        }
    };

    recognition.onend = function() {
        resetBtn();
    };

    function resetBtn() {
        isListening = false;
        btn.classList.remove('btn-danger');
        btn.classList.add('btn-outline-primary');
        label.textContent = '<?= e(__('entry.smart_entry')) ?>';
        setTimeout(function() { status.classList.add('d-none'); }, 5000);
    }
})();
</script>
