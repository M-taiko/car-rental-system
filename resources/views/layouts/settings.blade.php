<div class="modal fade" id="settingsModal" tabindex="-1" role="dialog" aria-labelledby="settingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-right" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="settingsModalLabel">{{ __('Settings') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Primary Color Settings -->
                <div class="form-group">
                    <label class="d-block">{{ __('Primary') }}</label>
                    <div class="d-flex flex-wrap">
                        <span class="color-box primary-color-box active" data-color="#007bff"></span>
                        <span class="color-box primary-color-box" data-color="#6f42c1"></span>
                        <span class="color-box primary-color-box" data-color="#28a745"></span>
                        <span class="color-box primary-color-box" data-color="#fd7e14"></span>
                        <span class="color-box primary-color-box" data-color="#ffc107"></span>
                        <span class="color-box primary-color-box" data-color="#17a2b8"></span>
                        <span class="color-box primary-color-box" data-color="#dc3545"></span>
                        <span class="color-box primary-color-box" data-color="#6c757d"></span>
                        <span class="color-box primary-color-box" data-color="#00ffff"></span>
                        <span class="color-box primary-color-box" data-color="#6610f2"></span>
                        <span class="color-box primary-color-box" data-color="#6f42c1"></span>
                        <span class="color-box primary-color-box" data-color="#6610f2"></span>
                        <span class="color-box primary-color-box" data-color="#fd7e14"></span>
                        <span class="color-box primary-color-box" data-color="#e83e8c"></span>
                        <span class="color-box primary-color-box" data-color="#fd7e14"></span>

                    </div>
                </div>

                <!-- Surface Color Settings -->
                <div class="form-group">
                    <label class="d-block">{{ __('Surface') }}</label>
                    <div class="d-flex flex-wrap">
                        <span class="color-box surface-color-box active" data-color="#f8f9fa"></span>
                        <span class="color-box surface-color-box" data-color="#e9ecef"></span>
                        <span class="color-box surface-color-box" data-color="#dee2e6"></span>
                        <span class="color-box surface-color-box" data-color="#ced4da"></span>
                        <span class="color-box surface-color-box" data-color="#adb5bd"></span>
                        <span class="color-box surface-color-box" data-color="#6c757d"></span>
                        <span class="color-box surface-color-box" data-color="#495057"></span>
                        <span class="color-box surface-color-box" data-color="#343a40"></span>
                        <span class="color-box surface-color-box" data-color="#212529"></span>
                    </div>
                </div>

                <!-- Presets -->
                <div class="form-group">
                    <label class="d-block">{{ __('Presets') }}</label>
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-outline-primary active" data-preset="aura">
                            <input type="radio" name="presets" id="aura" autocomplete="off" checked> Aura
                        </label>
                        <label class="btn btn-outline-primary" data-preset="lara">
                            <input type="radio" name="presets" id="lara" autocomplete="off"> Lara
                        </label>
                    </div>
                </div>

                <!-- Color Scheme -->
                <div class="form-group">
                    <label class="d-block">{{ __('Color Scheme') }}</label>
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-outline-primary active" data-scheme="light">
                            <input type="radio" name="colorScheme" id="lightScheme" autocomplete="off" checked> Light
                        </label>
                        <label class="btn btn-outline-primary" data-scheme="dark">
                            <input type="radio" name="colorScheme" id="darkScheme" autocomplete="off"> Dark
                        </label>
                    </div>
                </div>

                <!-- Menu Type -->
                <div class="form-group">
                    <label class="d-block">{{ __('Menu Type') }}</label>
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-outline-primary active" data-menu-type="static">
                            <input type="radio" name="menuType" id="staticMenu" autocomplete="off" checked> Static
                        </label>
                        <label class="btn btn-outline-primary" data-menu-type="overlay">
                            <input type="radio" name="menuType" id="overlayMenu" autocomplete="off"> Overlay
                        </label>
                        <label class="btn btn-outline-primary" data-menu-type="slim">
                            <input type="radio" name="menuType" id="slimMenu" autocomplete="off"> Slim
                        </label>
                        <label class="btn btn-outline-primary" data-menu-type="slim-plus">
                            <input type="radio" name="menuType" id="slimPlusMenu" autocomplete="off"> Slim+
                        </label>
                    </div>
                </div>

                <!-- Layout Theme -->
                <div class="form-group">
                    <label class="d-block">{{ __('Layout Theme') }}</label>
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-outline-primary active" data-layout-theme="color-scheme">
                            <input type="radio" name="layoutTheme" id="colorSchemeLayout" autocomplete="off" checked> Color Scheme
                        </label>
                        <label class="btn btn-outline-primary" data-layout-theme="primary-color-light-only">
                            <input type="radio" name="layoutTheme" id="primaryColorLightOnlyLayout" autocomplete="off"> Primary Color (Light Only)
                        </label>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                <button type="button" class="btn btn-primary" id="applySettings">{{ __('Apply Settings') }}</button>
            </div>
        </div>
    </div>
</div>

<style>
    .modal-right .modal-dialog {
        position: fixed;
        margin: auto;
        width: 320px;
        height: 100%;
        -webkit-transform: translate3d(0%, 0, 0);
        -ms-transform: translate3d(0%, 0, 0);
        transform: translate3d(0%, 0, 0);
    }
    .modal-right .modal-content {
        height: 100%;
        overflow-y: auto;
    }
    .modal.fade .modal-dialog.modal-right {
        right: -320px;
        -webkit-transition: opacity 0.3s linear, right 0.3s ease-out;
        -moz-transition: opacity 0.3s linear, right 0.3s ease-out;
        -o-transition: opacity 0.3s linear, right 0.3s ease-out;
        transition: opacity 0.3s linear, right 0.3s ease-out;
    }
    .modal.fade.show .modal-dialog.modal-right {
        right: 0;
    }
    .modal-right .modal-header {
        border-bottom: none;
        padding: 1.5rem;
    }
    .modal-right .modal-body {
        padding: 1.5rem;
    }
    .modal-right .modal-footer {
        border-top: none;
        padding: 1.5rem;
    }
    .color-box {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        cursor: pointer;
        margin: 5px;
        border: 2px solid transparent;
        display: inline-block;
    }
    .color-box.active {
        border-color: #007bff; /* Highlight active color */
    }
    .primary-color-box[data-color="#007bff"] { background-color: #007bff; }
    .primary-color-box[data-color="#6f42c1"] { background-color: #6f42c1; }
    .primary-color-box[data-color="#28a745"] { background-color: #28a745; }
    .primary-color-box[data-color="#fd7e14"] { background-color: #fd7e14; }
    .primary-color-box[data-color="#ffc107"] { background-color: #ffc107; }
    .primary-color-box[data-color="#17a2b8"] { background-color: #17a2b8; }
    .primary-color-box[data-color="#dc3545"] { background-color: #dc3545; }
    .primary-color-box[data-color="#6c757d"] { background-color: #6c757d; }
    .primary-color-box[data-color="#00ffff"] { background-color: #00ffff; }
    .primary-color-box[data-color="#6610f2"] { background-color: #6610f2; }
    .primary-color-box[data-color="#e83e8c"] { background-color: #e83e8c; }

    .surface-color-box[data-color="#f8f9fa"] { background-color: #f8f9fa; border: 1px solid #eee; }
    .surface-color-box[data-color="#e9ecef"] { background-color: #e9ecef; border: 1px solid #eee; }
    .surface-color-box[data-color="#dee2e6"] { background-color: #dee2e6; border: 1px solid #eee; }
    .surface-color-box[data-color="#ced4da"] { background-color: #ced4da; border: 1px solid #eee; }
    .surface-color-box[data-color="#adb5bd"] { background-color: #adb5bd; border: 1px solid #eee; }
    .surface-color-box[data-color="#6c757d"] { background-color: #6c757d; border: 1px solid #eee; }
    .surface-color-box[data-color="#495057"] { background-color: #495057; border: 1px solid #eee; }
    .surface-color-box[data-color="#343a40"] { background-color: #343a40; border: 1px solid #eee; }
    .surface-color-box[data-color="#212529"] { background-color: #212529; border: 1px solid #eee; }
</style>
