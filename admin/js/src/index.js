'use strict';

import * as lit from 'lit';
import * as decorators_js from 'lit/decorators.js';
import * as Lottie from 'lottie-web';
import * as fflate from 'fflate';

exports.PlayerState = void 0;
(function(PlayerState) {
    PlayerState["Completed"] = "completed";
    PlayerState["Destroyed"] = "destroyed";
    PlayerState["Error"] = "error";
    PlayerState["Frozen"] = "frozen";
    PlayerState["Loading"] = "loading";
    PlayerState["Paused"] = "paused";
    PlayerState["Playing"] = "playing";
    PlayerState["Stopped"] = "stopped";
})(exports.PlayerState || (exports.PlayerState = {}));
exports.PlayMode = void 0;
(function(PlayMode) {
    PlayMode["Bounce"] = "bounce";
    PlayMode["Normal"] = "normal";
})(exports.PlayMode || (exports.PlayMode = {}));
exports.PlayerEvents = void 0;
(function(PlayerEvents) {
    PlayerEvents["Complete"] = "complete";
    PlayerEvents["Destroyed"] = "destroyed";
    PlayerEvents["Error"] = "error";
    PlayerEvents["Frame"] = "frame";
    PlayerEvents["Freeze"] = "freeze";
    PlayerEvents["Load"] = "load";
    PlayerEvents["Loop"] = "loop";
    PlayerEvents["Next"] = "next";
    PlayerEvents["Pause"] = "pause";
    PlayerEvents["Play"] = "play";
    PlayerEvents["Previous"] = "previous";
    PlayerEvents["Ready"] = "ready";
    PlayerEvents["Rendered"] = "rendered";
    PlayerEvents["Stop"] = "stop";
})(exports.PlayerEvents || (exports.PlayerEvents = {}));
class CustomError extends Error {
}
const addExt = (ext, str)=>{
    if (!str) return;
    if (getExt(str)) {
        if (getExt(str) === ext) return str;
        return `${getFilename(str)}.${ext}`;
    }
    return `${str}.${ext}`;
}, aspectRatio = (objectFit)=>{
    switch(objectFit){
        case 'contain':
        case 'scale-down':
            return 'xMidYMid meet';
        case 'cover':
            return 'xMidYMid slice';
        case 'fill':
            return 'none';
        case 'none':
            return 'xMinYMin slice';
        default:
            return 'xMidYMid meet';
    }
}, base64ToU8 = (str)=>fflate.strToU8(isServer() ? Buffer.from(parseBase64(str), 'base64').toString('binary') : atob(parseBase64(str)), true), createDotLottie = async ({ animations, manifest, fileName, shouldDownload = true })=>{
    try {
        if (!animations?.length || !manifest) {
            throw new Error(`Missing or malformed required parameter(s):\n ${!animations?.length ? '- animations\n' : ''} ${!manifest ? '- manifest \n' : ''}`);
        }
        const name = addExt('lottie', fileName) || `${useId()}.lottie`, dotlottie = {
            'manifest.json': [
                fflate.strToU8(JSON.stringify(manifest), true),
                {
                    level: 0
                }
            ]
        };
        for (const [i, animation] of animations.entries()){
            for (const asset of animation.assets ?? []){
                if (!asset.p || !isImage(asset) && !isAudio(asset)) {
                    continue;
                }
                const { p: file, u: path } = asset, assetId = asset.id || useId(), isEncoded = file.startsWith('data:'), ext = isEncoded ? getExtFromB64(file) : getExt(file), dataURL = isEncoded ? file : await fileToBase64(path ? path.endsWith('/') && `${path}${file}` || `${path}/${file}` : file);
                asset.p = `${assetId}.${ext}`;
                asset.u = '';
                asset.e = 1;
                dotlottie[`${isAudio(asset) ? 'audio' : 'images'}/${assetId}.${ext}`] = [
                    base64ToU8(dataURL),
                    {
                        level: 9
                    }
                ];
            }
            dotlottie[`animations/${manifest.animations[i].id}.json`] = [
                fflate.strToU8(JSON.stringify(animation), true),
                {
                    level: 9
                }
            ];
        }
        const buffer = await getArrayBuffer(dotlottie);
        return shouldDownload ? download(buffer, {
            name,
            mimeType: 'application/zip'
        }) : buffer;
    } catch (err) {
        console.error(`❌ ${handleErrors(err).message}`);
    }
}, createJSON = ({ animation, fileName, shouldDownload })=>{
    try {
        if (!animation) {
            throw new Error('Missing or malformed required parameter(s):\n - animation\n\'');
        }
        const name = addExt('json', fileName) || `${useId()}.json`, jsonString = JSON.stringify(animation);
        return shouldDownload ? download(jsonString, {
            name,
            mimeType: 'application/json'
        }) : jsonString;
    } catch (err) {
        console.error(`❌ ${handleErrors(err).message}`);
    }
}, download = (data, options)=>{
    const blob = new Blob([
        data
    ], {
        type: options?.mimeType
    }), fileName = options?.name || useId(), dataURL = URL.createObjectURL(blob), link = document.createElement('a');
    link.href = dataURL;
    link.download = fileName;
    link.hidden = true;
    document.body.appendChild(link);
    link.click();
    setTimeout(()=>{
        link.remove();
        URL.revokeObjectURL(dataURL);
    }, 1000);
}, fileToBase64 = async (url)=>{
    const response = await fetch(url), blob = await response.blob();
    return new Promise((resolve, reject)=>{
        try {
            const reader = new FileReader();
            reader.onload = ()=>{
                if (typeof reader.result === 'string') {
                    resolve(reader.result);
                    return;
                }
                reject();
            };
            reader.readAsDataURL(blob);
        } catch (e) {
            reject(e);
        }
    });
}, frameOutput = (frame)=>((frame ?? 0) + 1).toString().padStart(3, '0'), getAnimationData = async (input)=>{
    try {
        if (!input || typeof input !== 'string' && typeof input !== 'object') {
            throw new Error('Broken file or invalid file format');
        }
        if (typeof input !== 'string') {
            const animations = Array.isArray(input) ? input : [
                input
            ];
            return {
                animations,
                manifest: undefined,
                isDotLottie: false
            };
        }
        const result = await fetch(input);
        if (!result.ok) {
            const error = new CustomError(result.statusText);
            error.status = result.status;
            throw error;
        }
        const ext = getExt(input);
        if (ext === 'json' || !ext) {
            if (ext) {
                const lottie = await result.json();
                return {
                    animations: [
                        lottie
                    ],
                    manifest: undefined,
                    isDotLottie: false
                };
            }
            const text = await result.clone().text();
            try {
                const lottie = JSON.parse(text);
                return {
                    animations: [
                        lottie
                    ],
                    manifest: undefined,
                    isDotLottie: false
                };
            } catch (e) {
                console.warn(e);
            }
        }
        const { data, manifest } = await getLottieJSON(result);
        return {
            animations: data,
            manifest,
            isDotLottie: true
        };
    } catch (err) {
        console.error(`❌ ${handleErrors(err).message}`);
        return {
            animations: undefined,
            manifest: undefined,
            isDotLottie: false
        };
    }
}, getArrayBuffer = async (zippable)=>{
    const arrayBuffer = await new Promise((resolve, reject)=>{
        fflate.zip(zippable, {
            level: 9
        }, (err, data)=>{
            if (err) {
                reject(err);
                return;
            }
            resolve(data.buffer);
        });
    });
    return arrayBuffer;
}, getExt = (str)=>{
    if (!str || !hasExt(str)) return;
    return str.split('.').pop()?.toLowerCase();
}, getExtFromB64 = (str)=>{
    const mime = str.split(':')[1].split(';')[0];
    return mime.split('/')[1].split('+')[0];
}, getFilename = (src, keepExt)=>{
    const ext = getExt(src);
    return `${src.split('/').pop()?.replace(/\.[^.]*$/, '').replace(/\W+/g, '')}${keepExt && ext ? `.${ext}` : ''}`;
}, getLottieJSON = async (resp)=>{
    const unzipped = await unzip(resp), manifest = getManifest(unzipped), data = [], toResolve = [];
    for (const { id } of manifest.animations){
        const str = fflate.strFromU8(unzipped[`animations/${id}.json`]), lottie = JSON.parse(str);
        toResolve.push(resolveAssets(unzipped, lottie.assets));
        data.push(lottie);
    }
    await Promise.all(toResolve);
    return {
        data,
        manifest
    };
}, getManifest = (unzipped)=>{
    const file = fflate.strFromU8(unzipped['manifest.json'], false), manifest = JSON.parse(file);
    if (!('animations' in manifest)) throw new Error('Manifest not found');
    if (!manifest.animations.length) throw new Error('No animations listed in manifest');
    return manifest;
}, getMimeFromExt = (ext)=>{
    switch(ext){
        case 'svg':
        case 'svg+xml':
            return 'image/svg+xml';
        case 'jpg':
        case 'jpeg':
            return 'image/jpeg';
        case 'png':
        case 'gif':
        case 'webp':
            return `image/${ext}`;
        case 'mp3':
        case 'mpeg':
        case 'wav':
            return `audio/${ext}`;
        default:
            return '';
    }
}, handleErrors = (err)=>{
    const res = {
        message: 'Unknown error',
        status: isServer() ? 500 : 400
    };
    if (err && typeof err === 'object') {
        if ('message' in err && typeof err.message === 'string') {
            res.message = err.message;
        }
        if ('status' in err) {
            res.status = Number(err.status);
        }
    }
    return res;
}, hasExt = (path)=>{
    const lastDotIndex = path?.split('/').pop()?.lastIndexOf('.');
    return (lastDotIndex ?? 0) > 1 && path && path.length - 1 > (lastDotIndex ?? 0);
}, isAudio = (asset)=>!('h' in asset) && !('w' in asset) && 'p' in asset && 'e' in asset && 'u' in asset && 'id' in asset, isBase64 = (str)=>{
    if (!str) return false;
    const regex = /^([0-9a-zA-Z+/]{4})*(([0-9a-zA-Z+/]{2}==)|([0-9a-zA-Z+/]{3}=))?$/;
    return regex.test(parseBase64(str));
}, isImage = (asset)=>'w' in asset && 'h' in asset && !('xt' in asset) && 'p' in asset, isServer = ()=>!(typeof window !== 'undefined' && window.document), parseBase64 = (str)=>str.substring(str.indexOf(',') + 1), resolveAssets = async (unzipped, assets)=>{
    if (!Array.isArray(assets)) return;
    const toResolve = [];
    for (const asset of assets){
        if (!isAudio(asset) && !isImage(asset)) continue;
        const type = isImage(asset) ? 'images' : 'audio', u8 = unzipped?.[`${type}/${asset.p}`];
        if (!u8) continue;
        toResolve.push(new Promise((resolveAsset)=>{
            const assetB64 = isServer() ? Buffer.from(u8).toString('base64') : btoa(u8.reduce((dat, byte)=>`${dat}${String.fromCharCode(byte)}`, ''));
            asset.p = asset.p?.startsWith('data:') || isBase64(asset.p) ? asset.p : `data:${getMimeFromExt(getExt(asset.p))};base64,${assetB64}`;
            asset.e = 1;
            asset.u = '';
            resolveAsset();
        }));
    }
    await Promise.all(toResolve);
}, unzip = async (resp)=>{
    const u8 = new Uint8Array(await resp.arrayBuffer()), unzipped = await new Promise((resolve, reject)=>{
        fflate.unzip(u8, (err, file)=>{
            if (err) {
                reject(err);
            }
            resolve(file);
        });
    });
    return unzipped;
}, useId = (prefix)=>{
    const s4 = ()=>{
        return ((1 + Math.random()) * 0x10000 | 0).toString(16).substring(1);
    };
    return `${prefix ?? `:${s4()}`}-${s4()}`;
};

var name="dotlottie-player";var version="2.5.5";var description="Web Component for playing Lottie animations in your web app. Previously @johanaarstein/dotlottie-player";var exports$1={".":{"import":"./dist/esm/index.js",node:"./dist/esm/index.js",require:"./dist/cjs/index.js",types:"./dist/index.d.ts"}};var main="./dist/esm/index.js";var unpkg="./dist/index.js";var module$1="./dist/esm/index.js";var types="./dist/index.d.ts";var type="module";var homepage="https://www.aarstein.media/en/dotlottie-player";var repository={url:"https://github.com/aarsteinmedia/dotlottie-player.git",type:"git"};var bugs="https://github.com/aarsteinmedia/dotlottie-player/issues";var author={name:"Johan Martin Aarstein",email:"johan@aarstein.media",url:"https://www.aarstein.media",organization:"Aarstein Media"};var license="GPL-2.0-or-later";var scripts={build:"rimraf ./dist && rollup -c","build:types":"rimraf ./types && tsc","build:cem":"npx cem analyze --config cem.config.mjs",prod:"pnpm build:types && pnpm build && pnpm build:cem",dev:"rollup -c -w --environment NODE_ENV:development",lint:"tsc && eslint . --ext .ts","lint:fix":"eslint . --ext .ts --fix"};var dependencies={fflate:"^0.8.2",lit:"^3.1.2","lottie-web":"^5.12.2"};var peerDependencies={"@types/react":">= 16.0.0"};var devDependencies={"@custom-elements-manifest/analyzer":"^0.9.4","@rollup/plugin-commonjs":"^25.0.7","@rollup/plugin-json":"^6.1.0","@rollup/plugin-node-resolve":"^15.2.3","@rollup/plugin-replace":"^5.0.5","@swc/core":"^1.4.8","@types/node":"^20.11.30","@typescript-eslint/eslint-plugin":"^7.3.1","@typescript-eslint/parser":"^7.3.1",autoprefixer:"^10.4.18","esbuild-sass-plugin":"^3.2.0",eslint:"^8.57.0","eslint-plugin-lit":"^1.11.0","postcss-flexbugs-fixes":"^5.0.2",rimraf:"^5.0.5",rollup:"^4.13.0","rollup-plugin-dts":"^6.1.0","rollup-plugin-html-literals":"^1.1.8","rollup-plugin-livereload":"^2.0.5","rollup-plugin-postcss":"^4.0.2","rollup-plugin-postcss-lit":"^2.1.0","rollup-plugin-serve":"^1.1.1","rollup-plugin-summary":"^2.0.0","rollup-plugin-swc3":"^0.11.0",sass:"^1.72.0","ts-lit-plugin":"^2.0.2",typescript:"^5.4.2"};var customElements="dist/custom-elements.json";var files=["dist","README.md"];var keywords=["lottie","dotlottie","animation","web component","component","lit-element","svg","vector","player"];var publishConfig={access:"public"};var engines={node:">= 8.17.0"};var funding={type:"paypal",url:"https://www.paypal.com/donate/?hosted_button_id=E7C7DMN8KSQ6A"};var pkg = {name:name,version:version,description:description,exports:exports$1,main:main,unpkg:unpkg,module:module$1,types:types,type:type,homepage:homepage,repository:repository,bugs:bugs,author:author,license:license,scripts:scripts,dependencies:dependencies,peerDependencies:peerDependencies,devDependencies:devDependencies,customElements:customElements,files:files,keywords:keywords,publishConfig:publishConfig,engines:engines,funding:funding};

var css_248z = lit.css`*{box-sizing:border-box}:host{--lottie-player-toolbar-height:35px;--lottie-player-toolbar-background-color:#FFF;--lottie-player-toolbar-icon-color:#000;--lottie-player-toolbar-icon-hover-color:#000;--lottie-player-toolbar-icon-active-color:#4285f4;--lottie-player-seeker-track-color:rgba(0, 0, 0, 0.2);--lottie-player-seeker-thumb-color:#4285f4;--lottie-player-seeker-display:block;display:block;width:100%;height:100%}@media (prefers-color-scheme:dark){:host{--lottie-player-toolbar-background-color:#000;--lottie-player-toolbar-icon-color:#FFF;--lottie-player-toolbar-icon-hover-color:#FFF;--lottie-player-seeker-track-color:rgba(255, 255, 255, 0.6)}}.main{display:flex;flex-direction:column;height:100%;width:100%;margin:0;padding:0}.animation{width:100%;height:100%;display:flex}[data-controls=true] .animation{height:calc(100% - 35px)}.animation-container{position:relative}.popover{position:absolute;right:5px;bottom:40px;background-color:var(--lottie-player-toolbar-background-color);border-radius:5px;padding:10px 15px;border:solid 2px var(--lottie-player-toolbar-icon-color);animation:fadeIn .2s ease-in-out}.popover::before{content:"";right:10px;border:7px solid transparent;border-top-color:transparent;margin-right:-7px;height:0;width:0;position:absolute;pointer-events:none;top:100%;border-top-color:var(--lottie-player-toolbar-icon-color)}.toolbar{display:flex;align-items:center;justify-items:center;background:var(--lottie-player-toolbar-background-color);margin:0;height:35px;padding:5px;border-radius:5px;gap:5px}.toolbar.has-error{pointer-events:none;opacity:.5}.toolbar button{cursor:pointer;fill:var(--lottie-player-toolbar-icon-color);color:var(--lottie-player-toolbar-icon-color);display:flex;background:0 0;border:0;padding:0;outline:0;height:100%;margin:0;align-items:center;gap:5px;opacity:.9}.toolbar button:hover{opacity:1}.toolbar button[data-active=true]{opacity:1;fill:var(--lottie-player-toolbar-icon-active-color)}.toolbar button:disabled{opacity:.5}.toolbar button:focus{outline:0}.toolbar button svg{pointer-events:none}.toolbar button svg>*{fill:inherit}.toolbar button.disabled svg{display:none}.progress-container{position:relative;width:100%}.progress-container.simple{margin-right:12px}.seeker{-webkit-appearance:none;-moz-appearance:none;appearance:none;outline:0}.seeker::-webkit-slider-runnable-track,.seeker::-webkit-slider-thumb{-webkit-appearance:none;appearance:none;outline:0}progress{-webkit-appearance:none;-moz-appearance:none;appearance:none;outline:0}.seeker{width:100%;height:20px;border-radius:3px;border:0;cursor:pointer;background-color:transparent;display:var(--lottie-player-seeker-display);color:var(--lottie-player-seeker-thumb-color);margin:0;padding:7.5px 0;position:relative;z-index:1}progress{position:absolute;width:100%;height:5px;border-radius:3px;border:0;top:0;left:0;margin:7.5px 0;background-color:var(--lottie-player-seeker-track-color);pointer-events:none}::-moz-progress-bar{background-color:var(--lottie-player-seeker-thumb-color)}::-webkit-progress-inner-element{border-radius:3px;overflow:hidden}::-webkit-slider-runnable-track{background-color:transparent}::-webkit-progress-value{background-color:var(--lottie-player-seeker-thumb-color)}.seeker::-webkit-slider-thumb{height:15px;width:15px;border-radius:50%;border:0;background-color:var(--lottie-player-seeker-thumb-color);cursor:pointer;-webkit-transition:transform .2s ease-in-out;transition:transform .2s ease-in-out;transform:scale(0)}.seeker:focus::-webkit-slider-thumb,.seeker:hover::-webkit-slider-thumb{transform:scale(1)}.seeker::-moz-range-progress{background-color:var(--lottie-player-seeker-thumb-color);height:5px;border-radius:3px}.seeker::-moz-range-thumb{height:15px;width:15px;border-radius:50%;background-color:var(--lottie-player-seeker-thumb-color);border:0;cursor:pointer;-moz-transition:transform .2s ease-in-out;transition:transform .2s ease-in-out;transform:scale(0)}.seeker:focus::-moz-range-thumb,.seeker:hover::-moz-range-thumb{transform:scale(1)}.seeker::-ms-track{width:100%;height:5px;cursor:pointer;background:0 0;border-color:transparent;color:transparent}.seeker::-ms-fill-upper{background:var(--lottie-player-seeker-track-color);border-radius:3px}.seeker::-ms-fill-lower{background-color:var(--lottie-player-seeker-thumb-color);border-radius:3px}.seeker::-ms-thumb{border:0;height:15px;width:15px;border-radius:50%;background:var(--lottie-player-seeker-thumb-color);cursor:pointer;-ms-transition:transform .2s ease-in-out;transition:transform .2s ease-in-out;transform:scale(0)}.seeker:hover::-ms-thumb{transform:scale(1)}.seeker:focus::-ms-thumb{transform:scale(1)}.seeker:focus::-ms-fill-lower,.seeker:focus::-ms-fill-upper{background:var(--lottie-player-seeker-track-color)}.error{display:flex;margin:auto;justify-content:center;height:100%;align-items:center}.error svg{width:100%;height:auto}@keyframes fadeIn{0%{opacity:0}100%{opacity:1}}`;

function _ts_decorate(decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof undefined === "function") r = undefined(decorators, target, key, desc);
    else for(var i = decorators.length - 1; i >= 0; i--)if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
}
class DotLottiePlayer extends lit.LitElement {
    _getOptions() {
        const preserveAspectRatio = this.preserveAspectRatio ?? (this.objectfit && aspectRatio(this.objectfit)), currentAnimationSettings = this.multiAnimationSettings?.[this._currentAnimation], currentAnimationManifest = this._manifest.animations?.[this._currentAnimation], loop = currentAnimationSettings?.loop !== undefined ? !!currentAnimationSettings.loop : this.loop !== undefined ? !!this.loop : currentAnimationManifest.loop !== undefined && !!currentAnimationManifest.loop, autoplay = !this.animateOnScroll && (currentAnimationSettings?.autoplay !== undefined ? !!currentAnimationSettings.autoplay : this.autoplay !== undefined ? !!this.autoplay : currentAnimationManifest.autoplay !== undefined && !!currentAnimationManifest.autoplay), initialSegment = !this.segment || this.segment.some((val)=>val < 0) ? undefined : this.segment.every((val)=>val > 0) ? [
            this.segment[0] - 1,
            this.segment[1] - 1
        ] : this.segment, options = {
            container: this.container,
            loop,
            autoplay,
            renderer: this.renderer,
            initialSegment,
            rendererSettings: {
                imagePreserveAspectRatio: preserveAspectRatio
            }
        };
        switch(this.renderer){
            case 'svg':
                options.rendererSettings = {
                    ...options.rendererSettings,
                    hideOnTransparent: true,
                    preserveAspectRatio,
                    progressiveLoad: true
                };
                break;
            case 'canvas':
                options.rendererSettings = {
                    ...options.rendererSettings,
                    clearCanvas: true,
                    preserveAspectRatio,
                    progressiveLoad: true
                };
                break;
            case 'html':
                options.rendererSettings = {
                    ...options.rendererSettings,
                    hideOnTransparent: true
                };
        }
        return options;
    }
    _addIntersectionObserver() {
        if (this._intersectionObserver || !('IntersectionObserver' in window)) {
            return;
        }
        this._intersectionObserver = new IntersectionObserver((entries)=>{
            for (const entry of entries){
                if (!entry.isIntersecting || document.hidden) {
                    if (this.currentState === exports.PlayerState.Playing) {
                        this._freeze();
                    }
                    this._playerState.visible = false;
                    continue;
                }
                if (!this.animateOnScroll && this.currentState === exports.PlayerState.Frozen) {
                    this.play();
                }
                if (!this._playerState.scrollY) {
                    this._playerState.scrollY = scrollY;
                }
                this._playerState.visible = true;
            }
        });
        this._intersectionObserver.observe(this.container);
    }
    async load(src) {
        if (!this.shadowRoot) return;
        try {
            const { animations, manifest, isDotLottie } = await getAnimationData(src);
            if (!animations || animations.some((animation)=>!this._isLottie(animation))) {
                throw new Error('Broken or corrupted file');
            }
            this._isBounce = this.multiAnimationSettings?.[this._currentAnimation]?.mode !== undefined ? this.multiAnimationSettings?.[this._currentAnimation]?.mode === exports.PlayMode.Bounce : this.mode === exports.PlayMode.Bounce;
            this._isDotLottie = !!isDotLottie;
            this._animations = animations;
            this._manifest = manifest ?? {
                animations: [
                    {
                        id: useId(),
                        autoplay: !this.animateOnScroll && this.autoplay,
                        loop: this.loop,
                        direction: this.direction,
                        mode: this.mode,
                        speed: this.speed
                    }
                ]
            };
            if (this._lottieInstance) this._lottieInstance.destroy();
            this.currentState = this.autoplay && !this.animateOnScroll ? exports.PlayerState.Playing : exports.PlayerState.Stopped;
            this._lottieInstance = Lottie.loadAnimation({
                ...this._getOptions(),
                animationData: animations[this._currentAnimation]
            });
        } catch (err) {
            this._errorMessage = handleErrors(err).message;
            this.currentState = exports.PlayerState.Error;
            this.dispatchEvent(new CustomEvent(exports.PlayerEvents.Error));
            return;
        }
        this._addEventListeners();
        const speed = this.multiAnimationSettings?.[this._currentAnimation]?.speed ?? this.speed ?? this._manifest.animations[this._currentAnimation].speed, direction = this.multiAnimationSettings?.[this._currentAnimation]?.direction ?? this.direction ?? this._manifest.animations[this._currentAnimation].direction ?? 1;
        this.setSpeed(speed);
        this.setDirection(direction);
        this.setSubframe(!!this.subframe);
        if (this.autoplay || this.animateOnScroll) {
            if (this.direction === -1) this.seek('99%');
            if (!('IntersectionObserver' in window)) {
                !this.animateOnScroll && this.play();
                this._playerState.visible = true;
            }
            this._addIntersectionObserver();
            return;
        }
    }
    getManifest() {
        return this._manifest;
    }
    _addEventListeners() {
        if (!this._lottieInstance) return;
        this._lottieInstance.addEventListener('enterFrame', this._enterFrame);
        this._lottieInstance.addEventListener('complete', this._complete);
        this._lottieInstance.addEventListener('loopComplete', this._loopComplete);
        this._lottieInstance.addEventListener('DOMLoaded', this._DOMLoaded);
        this._lottieInstance.addEventListener('data_ready', this._dataReady);
        this._lottieInstance.addEventListener('data_failed', this._dataFailed);
        if (this.container && this.hover) {
            this.container.addEventListener('mouseenter', this._mouseEnter);
            this.container.addEventListener('mouseleave', this._mouseLeave);
        }
        addEventListener('focus', this._handleWindowBlur, {
            passive: true,
            capture: true
        });
        addEventListener('blur', this._handleWindowBlur, {
            passive: true,
            capture: true
        });
        if (this.animateOnScroll) {
            addEventListener('scroll', this._handleScroll, {
                passive: true,
                capture: true
            });
        }
    }
    _removeEventListeners() {
        if (!this._lottieInstance || !this.container) return;
        this._lottieInstance.removeEventListener('enterFrame', this._enterFrame);
        this._lottieInstance.removeEventListener('complete', this._complete);
        this._lottieInstance.removeEventListener('loopComplete', this._loopComplete);
        this._lottieInstance.removeEventListener('DOMLoaded', this._DOMLoaded);
        this._lottieInstance.removeEventListener('data_ready', this._dataReady);
        this._lottieInstance.removeEventListener('data_failed', this._dataFailed);
        this.container.removeEventListener('mouseenter', this._mouseEnter);
        this.container.removeEventListener('mouseleave', this._mouseLeave);
        removeEventListener('focus', this._handleWindowBlur, true);
        removeEventListener('blur', this._handleWindowBlur, true);
        removeEventListener('scroll', this._handleScroll, true);
    }
    _loopComplete() {
        if (!this._lottieInstance) {
            return;
        }
        const { firstFrame, totalFrames, playDirection } = this._lottieInstance;
        if (this.count) {
            this._isBounce ? this._playerState.count += 1 : this._playerState.count += 0.5;
            if (this._playerState.count >= this.count) {
                this.setLooping(false);
                this.currentState = exports.PlayerState.Completed;
                this.dispatchEvent(new CustomEvent(exports.PlayerEvents.Complete));
                return;
            }
        }
        this.dispatchEvent(new CustomEvent(exports.PlayerEvents.Loop));
        if (this._isBounce) {
            this._lottieInstance.goToAndStop(playDirection === -1 ? firstFrame : totalFrames * 0.99, true);
            this._lottieInstance.setDirection(playDirection * -1);
            return setTimeout(()=>{
                !this.animateOnScroll && this._lottieInstance?.play();
            }, this.intermission);
        }
        this._lottieInstance.goToAndStop(playDirection === -1 ? totalFrames * 0.99 : firstFrame, true);
        return setTimeout(()=>{
            !this.animateOnScroll && this._lottieInstance?.play();
        }, this.intermission);
    }
    _enterFrame() {
        if (!this._lottieInstance) {
            return;
        }
        const { currentFrame, totalFrames } = this._lottieInstance;
        this._seeker = Math.floor(currentFrame / totalFrames * 100);
        this.dispatchEvent(new CustomEvent(exports.PlayerEvents.Frame, {
            detail: {
                frame: currentFrame,
                seeker: this._seeker
            }
        }));
    }
    _complete() {
        if (!this._lottieInstance) {
            return;
        }
        if (this._animations.length > 1 && this.multiAnimationSettings?.[this._currentAnimation + 1]?.autoplay) {
            return this.next();
        }
        const { currentFrame, totalFrames } = this._lottieInstance;
        this._seeker = Math.floor(currentFrame / totalFrames * 100);
        this.currentState = exports.PlayerState.Completed;
        this.dispatchEvent(new CustomEvent(exports.PlayerEvents.Complete, {
            detail: {
                frame: currentFrame,
                seeker: this._seeker
            }
        }));
    }
    _DOMLoaded() {
        this._playerState.loaded = true;
        this.dispatchEvent(new CustomEvent(exports.PlayerEvents.Ready));
    }
    _dataReady() {
        this.dispatchEvent(new CustomEvent(exports.PlayerEvents.Load));
    }
    _dataFailed() {
        this.currentState = exports.PlayerState.Error;
        this.dispatchEvent(new CustomEvent(exports.PlayerEvents.Error));
    }
    _handleWindowBlur({ type }) {
        if (this.currentState === exports.PlayerState.Playing && type === 'blur') {
            this._freeze();
        }
        if (this.currentState === exports.PlayerState.Frozen && type === 'focus') {
            this.play();
        }
    }
    _mouseEnter() {
        if (this.hover && this.currentState !== exports.PlayerState.Playing) {
            this.play();
        }
    }
    _mouseLeave() {
        if (this.hover && this.currentState === exports.PlayerState.Playing) {
            this.stop();
        }
    }
    _onVisibilityChange() {
        if (document.hidden && this.currentState === exports.PlayerState.Playing) {
            this._freeze();
            return;
        }
        if (this.currentState === exports.PlayerState.Frozen) {
            this.play();
        }
    }
    _handleScroll() {
        if (!this.animateOnScroll || !this._lottieInstance) {
            return;
        }
        if (isServer()) {
            console.warn('DotLottie: Scroll animations might not work properly in a Server Side Rendering context. Try to wrap this in a client component.');
        }
        if (this._playerState.visible) {
            const adjustedScroll = scrollY > this._playerState.scrollY ? scrollY - this._playerState.scrollY : this._playerState.scrollY - scrollY, clampedScroll = Math.min(Math.max(adjustedScroll / 3, 1), this._lottieInstance.totalFrames * 3), roundedScroll = clampedScroll / 3;
            requestAnimationFrame(()=>{
                if (roundedScroll < (this._lottieInstance?.totalFrames ?? 0)) {
                    this.currentState = exports.PlayerState.Playing;
                    this._lottieInstance?.goToAndStop(roundedScroll, true);
                } else {
                    this.currentState = exports.PlayerState.Paused;
                }
            });
        }
        if (this._playerState.scrollTimeout) {
            clearTimeout(this._playerState.scrollTimeout);
        }
        this._playerState.scrollTimeout = setTimeout(()=>{
            this.currentState = exports.PlayerState.Paused;
        }, 400);
    }
    _handleSeekChange({ target }) {
        if (!(target instanceof HTMLInputElement) || !this._lottieInstance || isNaN(Number(target.value))) return;
        this.seek(Math.floor(Number(target.value) / 100 * this._lottieInstance.totalFrames));
        setTimeout(()=>{
            if (target.parentElement instanceof HTMLFormElement) {
                target.parentElement.reset();
            }
        }, 100);
    }
    _isLottie(json) {
        const mandatory = [
            'v',
            'ip',
            'op',
            'layers',
            'fr',
            'w',
            'h'
        ];
        return mandatory.every((field)=>Object.prototype.hasOwnProperty.call(json, field));
    }
    async addAnimation(configs, fileName, shouldDownload = true) {
        const { animations = [], manifest = {
            animations: this.src ? [
                {
                    id: this._identifier
                }
            ] : []
        } } = this.src ? await getAnimationData(this.src) : {};
        try {
            manifest.generator = pkg.name;
            for (const config of configs){
                const { url } = config, { animations: animationsToAdd } = await getAnimationData(url);
                if (!animationsToAdd) {
                    throw new Error('No animation loaded');
                }
                if (manifest.animations.some(({ id })=>id === config.id)) {
                    throw new Error('Duplicate id for animation');
                }
                manifest.animations = [
                    ...manifest.animations,
                    {
                        id: config.id
                    }
                ];
                animations?.push(...animationsToAdd);
            }
            return createDotLottie({
                animations,
                manifest,
                fileName,
                shouldDownload
            });
        } catch (err) {
            console.error(handleErrors(err).message);
        }
    }
    getLottie() {
        return this._lottieInstance;
    }
    play() {
        if (!this._lottieInstance) return;
        if (this.currentState) {
            this._playerState.prev = this.currentState;
        }
        this._lottieInstance.play();
        setTimeout(()=>{
            this.currentState = exports.PlayerState.Playing;
        }, 0);
        this.dispatchEvent(new CustomEvent(exports.PlayerEvents.Play));
    }
    pause() {
        if (!this._lottieInstance) return;
        if (this.currentState) {
            this._playerState.prev = this.currentState;
        }
        this._lottieInstance.pause();
        setTimeout(()=>{
            this.currentState = exports.PlayerState.Paused;
        }, 0);
        this.dispatchEvent(new CustomEvent(exports.PlayerEvents.Pause));
    }
    stop() {
        if (!this._lottieInstance) return;
        if (this.currentState) {
            this._playerState.prev = this.currentState;
        }
        this._playerState.count = 0;
        this._lottieInstance.stop();
        setTimeout(()=>{
            this.currentState = exports.PlayerState.Stopped;
        }, 0);
        this.dispatchEvent(new CustomEvent(exports.PlayerEvents.Stop));
    }
    destroy() {
        if (!this._lottieInstance) return;
        this.currentState = exports.PlayerState.Destroyed;
        this._lottieInstance.destroy();
        this._lottieInstance = null;
        this.dispatchEvent(new CustomEvent(exports.PlayerEvents.Destroyed));
        this.remove();
        document.removeEventListener('visibilitychange', this._onVisibilityChange);
    }
    seek(value) {
        if (!this._lottieInstance) return;
        const matches = value.toString().match(/^([0-9]+)(%?)$/);
        if (!matches) {
            return;
        }
        const frame = Math.floor(matches[2] === '%' ? this._lottieInstance.totalFrames * Number(matches[1]) / 100 : Number(matches[1]));
        this._seeker = frame;
        if (this.currentState === exports.PlayerState.Playing || this.currentState === exports.PlayerState.Frozen && this._playerState.prev === exports.PlayerState.Playing) {
            this._lottieInstance.goToAndPlay(frame, true);
            this.currentState = exports.PlayerState.Playing;
            return;
        }
        this._lottieInstance.goToAndStop(frame, true);
        this._lottieInstance.pause();
    }
    snapshot() {
        if (!this.shadowRoot) return;
        const svgElement = this.shadowRoot.querySelector('.animation svg'), data = svgElement instanceof Node ? new XMLSerializer().serializeToString(svgElement) : null;
        if (!data) {
            console.error('Could not serialize data');
            return;
        }
        download(data, {
            name: `${getFilename(this.src)}-${frameOutput(this._seeker)}.svg`,
            mimeType: 'image/svg+xml'
        });
        return data;
    }
    setSubframe(value) {
        if (!this._lottieInstance) return;
        this.subframe = value;
        this._lottieInstance.setSubframe(value);
    }
    setCount(value) {
        if (!this._lottieInstance) return;
        this.count = value;
    }
    _freeze() {
        if (!this._lottieInstance) return;
        if (this.currentState) {
            this._playerState.prev = this.currentState;
        }
        this._lottieInstance.pause();
        setTimeout(()=>{
            this.currentState = exports.PlayerState.Frozen;
        }, 0);
        this.dispatchEvent(new CustomEvent(exports.PlayerEvents.Freeze));
    }
    async reload() {
        if (!this._lottieInstance) return;
        this._lottieInstance.destroy();
        if (this.src) {
            await this.load(this.src);
        }
    }
    setSpeed(value = 1) {
        if (!this._lottieInstance) return;
        this.speed = value;
        this._lottieInstance.setSpeed(value);
    }
    setDirection(value) {
        if (!this._lottieInstance) return;
        this.direction = value;
        this._lottieInstance.setDirection(value);
    }
    setLooping(value) {
        if (!this._lottieInstance) {
            return;
        }
        this.loop = value;
        this._lottieInstance.setLoop(value);
    }
    setMultiAnimationSettings(settings) {
        if (!this._lottieInstance) {
            return;
        }
        this.multiAnimationSettings = settings;
    }
    togglePlay() {
        if (!this._lottieInstance) return;
        const { currentFrame, playDirection, totalFrames } = this._lottieInstance;
        if (this.currentState === exports.PlayerState.Playing) {
            return this.pause();
        }
        if (this.currentState !== exports.PlayerState.Completed) {
            return this.play();
        }
        this.currentState = exports.PlayerState.Playing;
        if (this._isBounce) {
            this.setDirection(playDirection * -1);
            return this._lottieInstance.goToAndPlay(currentFrame, true);
        }
        if (playDirection === -1) {
            return this._lottieInstance.goToAndPlay(totalFrames, true);
        }
        return this._lottieInstance.goToAndPlay(0, true);
    }
    toggleLooping() {
        this.setLooping(!this.loop);
    }
    toggleBoomerang() {
        const curr = this.multiAnimationSettings?.[this._currentAnimation];
        if (curr?.mode !== undefined) {
            if (curr.mode === exports.PlayMode.Normal) {
                curr.mode = exports.PlayMode.Bounce;
                this._isBounce = true;
                return;
            }
            curr.mode = exports.PlayMode.Normal;
            this._isBounce = false;
            return;
        }
        if (this.mode === exports.PlayMode.Normal) {
            this.mode = exports.PlayMode.Bounce;
            this._isBounce = true;
            return;
        }
        this.mode = exports.PlayMode.Normal;
        this._isBounce = false;
    }
    _toggleSettings(flag) {
        if (flag === undefined) {
            this._isSettingsOpen = !this._isSettingsOpen;
            return;
        }
        this._isSettingsOpen = flag;
    }
    _handleBlur() {
        setTimeout(()=>this._toggleSettings(false), 200);
    }
    _switchInstance(isPrevious = false) {
        if (!this._animations[this._currentAnimation]) return;
        try {
            if (this._lottieInstance) this._lottieInstance.destroy();
            this._lottieInstance = Lottie.loadAnimation({
                ...this._getOptions(),
                animationData: this._animations[this._currentAnimation]
            });
            if (this.multiAnimationSettings?.[this._currentAnimation]?.mode) {
                this._isBounce = this.multiAnimationSettings[this._currentAnimation].mode === exports.PlayMode.Bounce;
            }
            this._removeEventListeners();
            this._addEventListeners();
            this.dispatchEvent(new CustomEvent(isPrevious ? exports.PlayerEvents.Previous : exports.PlayerEvents.Next));
            if (this.multiAnimationSettings?.[this._currentAnimation]?.autoplay ?? this.autoplay) {
                if (this.animateOnScroll) {
                    this._lottieInstance?.goToAndStop(0, true);
                    this.currentState = exports.PlayerState.Paused;
                    return;
                }
                this._lottieInstance?.goToAndPlay(0, true);
                this.currentState = exports.PlayerState.Playing;
                return;
            }
            this._lottieInstance?.goToAndStop(0, true);
            this.currentState = exports.PlayerState.Stopped;
        } catch (err) {
            this._errorMessage = handleErrors(err).message;
            this.currentState = exports.PlayerState.Error;
            this.dispatchEvent(new CustomEvent(exports.PlayerEvents.Error));
        }
    }
    next() {
        this._currentAnimation++;
        this._switchInstance();
    }
    prev() {
        this._currentAnimation--;
        this._switchInstance(true);
    }
    async convert({ typeCheck, manifest, animations, src, fileName, shouldDownload = true }) {
        if (typeCheck || this._isDotLottie) {
            return createJSON({
                animation: (await getAnimationData(src || this.src))?.animations?.[0],
                fileName: `${getFilename(fileName || this.src)}.json`,
                shouldDownload
            });
        }
        return createDotLottie({
            animations: animations || (await getAnimationData(this.src))?.animations,
            manifest: {
                ...manifest || this._manifest,
                generator: pkg.name
            },
            fileName: `${getFilename(fileName || this.src)}.lottie`,
            shouldDownload
        });
    }
    static get styles() {
        return css_248z;
    }
    connectedCallback() {
        super.connectedCallback();
        if (typeof document.hidden !== 'undefined') {
            document.addEventListener('visibilitychange', this._onVisibilityChange);
        }
    }
    async firstUpdated() {
        this._addIntersectionObserver();
        if (this.src) {
            await this.load(this.src);
        }
        this.dispatchEvent(new CustomEvent(exports.PlayerEvents.Rendered));
    }
    disconnectedCallback() {
        super.disconnectedCallback();
        if (this._intersectionObserver) {
            this._intersectionObserver.disconnect();
            this._intersectionObserver = undefined;
        }
        if (this._lottieInstance) this._lottieInstance.destroy();
        document.removeEventListener('visibilitychange', this._onVisibilityChange);
    }
    renderControls() {
        const isPlaying = this.currentState === exports.PlayerState.Playing, isPaused = this.currentState === exports.PlayerState.Paused, isStopped = this.currentState === exports.PlayerState.Stopped, isError = this.currentState === exports.PlayerState.Error;
        return lit.html`<div class="${`lottie-controls toolbar ${isError ? 'has-error' : ''}`}" aria-label="Lottie Animation controls"><button @click="${this.togglePlay}" data-active="${isPlaying || isPaused}" tabindex="0" aria-label="Toggle Play/Pause">${isPlaying ? lit.html`<svg width="24" height="24" aria-hidden="true" focusable="false"><path d="M14.016 5.016H18v13.969h-3.984V5.016zM6 18.984V5.015h3.984v13.969H6z"/></svg>` : lit.html`<svg width="24" height="24" aria-hidden="true" focusable="false"><path d="M8.016 5.016L18.985 12 8.016 18.984V5.015z"/></svg>`}</button> <button @click="${this.stop}" data-active="${isStopped}" tabindex="0" aria-label="Stop"><svg width="24" height="24" aria-hidden="true" focusable="false"><path d="M6 6h12v12H6V6z"/></svg></button> ${this._animations?.length > 1 ? lit.html`${this._currentAnimation > 0 ? lit.html`<button @click="${this.prev}" tabindex="0" aria-label="Previous animation"><svg width="24" height="24" aria-hidden="true" focusable="false"><path d="M17.9 18.2 8.1 12l9.8-6.2v12.4zm-10.3 0H6.1V5.8h1.5v12.4z"/></svg></button>` : lit.nothing} ${this._currentAnimation + 1 < this._animations?.length ? lit.html`<button @click="${this.next}" tabindex="0" aria-label="Next animation"><svg width="24" height="24" aria-hidden="true" focusable="false"><path d="m6.1 5.8 9.8 6.2-9.8 6.2V5.8zM16.4 5.8h1.5v12.4h-1.5z"/></svg></button>` : lit.nothing}` : lit.nothing}<form class="progress-container${this.simple ? ' simple' : ''}"><input class="seeker" type="range" min="0" max="100" step="1" .value="${this._seeker}" @change="${this._handleSeekChange}" @mousedown="${this._freeze}" aria-valuemin="0" aria-valuemax="100" role="slider" aria-valuenow="${this._seeker}" tabindex="0" aria-label="Slider for search"><progress max="100" .value="${this._seeker}"></progress></form>${this.simple ? lit.nothing : lit.html`<button @click="${this.toggleLooping}" data-active="${this.loop ?? lit.nothing}" tabindex="0" aria-label="Toggle looping"><svg width="24" height="24" aria-hidden="true" focusable="false"><path d="M17.016 17.016v-4.031h1.969v6h-12v3l-3.984-3.984 3.984-3.984v3h10.031zM6.984 6.984v4.031H5.015v-6h12v-3l3.984 3.984-3.984 3.984v-3H6.984z"/></svg></button> <button @click="${this.toggleBoomerang}" data-active="${this._isBounce}" aria-label="Toggle boomerang" tabindex="0"><svg width="24" height="24" aria-hidden="true" focusable="false"><path d="m11.8 13.2-.3.3c-.5.5-1.1 1.1-1.7 1.5-.5.4-1 .6-1.5.8-.5.2-1.1.3-1.6.3s-1-.1-1.5-.3c-.6-.2-1-.5-1.4-1-.5-.6-.8-1.2-.9-1.9-.2-.9-.1-1.8.3-2.6.3-.7.8-1.2 1.3-1.6.3-.2.6-.4 1-.5.2-.2.5-.2.8-.3.3 0 .7-.1 1 0 .3 0 .6.1.9.2.9.3 1.7.9 2.4 1.5.4.4.8.7 1.1 1.1l.1.1.4-.4c.6-.6 1.2-1.2 1.9-1.6.5-.3 1-.6 1.5-.7.4-.1.7-.2 1-.2h.9c1 .1 1.9.5 2.6 1.4.4.5.7 1.1.8 1.8.2.9.1 1.7-.2 2.5-.4.9-1 1.5-1.8 2-.4.2-.7.4-1.1.4-.4.1-.8.1-1.2.1-.5 0-.9-.1-1.3-.3-.8-.3-1.5-.9-2.1-1.5-.4-.4-.8-.7-1.1-1.1h-.3zm-1.1-1.1c-.1-.1-.1-.1 0 0-.3-.3-.6-.6-.8-.9-.5-.5-1-.9-1.6-1.2-.4-.3-.8-.4-1.3-.4-.4 0-.8 0-1.1.2-.5.2-.9.6-1.1 1-.2.3-.3.7-.3 1.1 0 .3 0 .6.1.9.1.5.4.9.8 1.2.5.4 1.1.5 1.7.5.5 0 1-.2 1.5-.5.6-.4 1.1-.8 1.6-1.3.1-.3.3-.5.5-.6zM13 12c.5.5 1 1 1.5 1.4.5.5 1.1.9 1.9 1 .4.1.8 0 1.2-.1.3-.1.6-.3.9-.5.4-.4.7-.9.8-1.4.1-.5 0-.9-.1-1.4-.3-.8-.8-1.2-1.7-1.4-.4-.1-.8-.1-1.2 0-.5.1-1 .4-1.4.7-.5.4-1 .8-1.4 1.2-.2.2-.4.3-.5.5z"/></svg></button> <button @click="${this._handleSettingsClick}" @blur="${this._handleBlur}" aria-label="Settings" aria-haspopup="true" aria-expanded="${!!this._isSettingsOpen}" aria-controls="${`${this._identifier}-settings`}"><svg width="24" height="24" aria-hidden="true" focusable="false"><circle cx="12" cy="5.4" r="2.5"/><circle cx="12" cy="12" r="2.5"/><circle cx="12" cy="18.6" r="2.5"/></svg></button><div id="${`${this._identifier}-settings`}" class="popover" style="display:${this._isSettingsOpen ? 'block' : 'none'}">${this._isDotLottie ? lit.nothing : lit.html`<button @click="${this.convert}" aria-label="Convert JSON animation to dotLottie format" tabindex="0"><svg width="24" height="24" aria-hidden="true" focusable="false"><path d="M17.016 17.016v-4.031h1.969v6h-12v3l-3.984-3.984 3.984-3.984v3h10.031zM6.984 6.984v4.031H5.015v-6h12v-3l3.984 3.984-3.984 3.984v-3H6.984z"/></svg> Convert to dotLottie</button>`} <button @click="${this.snapshot}" aria-label="Download still image" tabindex="0"><svg width="24" height="24" aria-hidden="true" focusable="false"><path d="M16.8 10.8 12 15.6l-4.8-4.8h3V3.6h3.6v7.2h3zM12 15.6H3v4.8h18v-4.8h-9zm7.8 2.4h-2.4v-1.2h2.4V18z"/></svg> Download still image</button></div>`}</div>`;
    }
    render() {
        return lit.html`<figure class="${'animation-container main'}" data-controls="${this.controls ?? false}" lang="${this.description ? document?.documentElement?.lang : 'en'}" role="img" aria-label="${this.description ?? 'Lottie animation'}" data-loaded="${this._playerState.loaded}"><div class="animation" style="background:${this.background}">${this.currentState === exports.PlayerState.Error ? lit.html`<div class="error"><svg preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="1920" height="1080" viewBox="0 0 1920 1080"><path fill="#fff" d="M0 0h1920v1080H0z"/><path fill="#3a6d8b" d="M1190.2 531 1007 212.4c-22-38.2-77.2-38-98.8.5L729.5 531.3c-21.3 37.9 6.1 84.6 49.5 84.6l361.9.3c43.7 0 71.1-47.3 49.3-85.2zM937.3 288.7c.2-7.5 3.3-23.9 23.2-23.9 16.3 0 23 16.1 23 23.5 0 55.3-10.7 197.2-12.2 214.5-.1 1-.9 1.7-1.9 1.7h-18.3c-1 0-1.8-.7-1.9-1.7-1.4-17.5-13.4-162.9-11.9-214.1zm24.2 283.8c-13.1 0-23.7-10.6-23.7-23.7s10.6-23.7 23.7-23.7 23.7 10.6 23.7 23.7-10.6 23.7-23.7 23.7zM722.1 644h112.6v34.4h-70.4V698h58.8v31.7h-58.8v22.6h72.4v36.2H722.1V644zm162 57.1h.6c8.3-12.9 18.2-17.8 31.3-17.8 3 0 5.1.4 6.3 1v32.6h-.8c-22.4-3.8-35.6 6.3-35.6 29.5v42.3h-38.2V685.5h36.4v15.6zm78.9 0h.6c8.3-12.9 18.2-17.8 31.3-17.8 3 0 5.1.4 6.3 1v32.6h-.8c-22.4-3.8-35.6 6.3-35.6 29.5v42.3h-38.2V685.5H963v15.6zm39.5 36.2c0-31.3 22.2-54.8 56.6-54.8 34.4 0 56.2 23.5 56.2 54.8s-21.8 54.6-56.2 54.6c-34.4-.1-56.6-23.3-56.6-54.6zm74 0c0-17.4-6.1-29.1-17.8-29.1-11.7 0-17.4 11.7-17.4 29.1 0 17.4 5.7 29.1 17.4 29.1s17.8-11.8 17.8-29.1zm83.1-36.2h.6c8.3-12.9 18.2-17.8 31.3-17.8 3 0 5.1.4 6.3 1v32.6h-.8c-22.4-3.8-35.6 6.3-35.6 29.5v42.3h-38.2V685.5h36.4v15.6z"/><path fill="none" d="M718.9 807.7h645v285.4h-645z"/><text fill="#3a6d8b" style="text-align:center;position:absolute;left:100%;font-size:47px;font-family:system-ui,-apple-system,BlinkMacSystemFont,'.SFNSText-Regular',sans-serif" x="50%" y="848.017" text-anchor="middle">${this._errorMessage}</text></svg></div>` : lit.nothing}</div>${this.controls ? this.renderControls() : lit.nothing}</figure>`;
    }
    constructor(){
        super();
        this.animateOnScroll = false;
        this.background = 'transparent';
        this.controls = false;
        this.currentState = exports.PlayerState.Loading;
        this.direction = 1;
        this.hover = false;
        this.intermission = 0;
        this.loop = false;
        this.mode = exports.PlayMode.Normal;
        this.objectfit = 'contain';
        this.renderer = 'svg';
        this.simple = false;
        this.speed = 1;
        this.subframe = false;
        this._isSettingsOpen = false;
        this._seeker = 0;
        this._currentAnimation = 0;
        this._lottieInstance = null;
        this._identifier = this.id || useId('dotlottie');
        this._errorMessage = 'Something went wrong';
        this._isBounce = false;
        this._isDotLottie = false;
        this._playerState = {
            prev: exports.PlayerState.Loading,
            count: 0,
            loaded: false,
            visible: false,
            scrollY: 0,
            scrollTimeout: null
        };
        this._handleSettingsClick = ({ target })=>{
            this._toggleSettings();
            if (target instanceof HTMLElement) {
                target.focus();
            }
        };
        this._complete = this._complete.bind(this);
        this._dataReady = this._dataReady.bind(this);
        this._dataFailed = this._dataFailed.bind(this);
        this._DOMLoaded = this._DOMLoaded.bind(this);
        this._enterFrame = this._enterFrame.bind(this);
        this._handleScroll = this._handleScroll.bind(this);
        this._handleSeekChange = this._handleSeekChange.bind(this);
        this._handleWindowBlur = this._handleWindowBlur.bind(this);
        this._loopComplete = this._loopComplete.bind(this);
        this._mouseEnter = this._mouseEnter.bind(this);
        this._mouseLeave = this._mouseLeave.bind(this);
        this._onVisibilityChange = this._onVisibilityChange.bind(this);
        this._switchInstance = this._switchInstance.bind(this);
        this.convert = this.convert.bind(this);
        this.destroy = this.destroy.bind(this);
    }
}
_ts_decorate([
    decorators_js.property({
        type: Boolean
    })
], DotLottiePlayer.prototype, "animateOnScroll", void 0);
_ts_decorate([
    decorators_js.property({
        type: Boolean,
        reflect: true
    })
], DotLottiePlayer.prototype, "autoplay", void 0);
_ts_decorate([
    decorators_js.property({
        type: String
    })
], DotLottiePlayer.prototype, "background", void 0);
_ts_decorate([
    decorators_js.property({
        type: Boolean,
        reflect: true
    })
], DotLottiePlayer.prototype, "controls", void 0);
_ts_decorate([
    decorators_js.property({
        type: Number
    })
], DotLottiePlayer.prototype, "count", void 0);
_ts_decorate([
    decorators_js.property({
        type: String
    })
], DotLottiePlayer.prototype, "currentState", void 0);
_ts_decorate([
    decorators_js.property({
        type: String
    })
], DotLottiePlayer.prototype, "description", void 0);
_ts_decorate([
    decorators_js.property({
        type: Number
    })
], DotLottiePlayer.prototype, "direction", void 0);
_ts_decorate([
    decorators_js.property({
        type: Boolean
    })
], DotLottiePlayer.prototype, "hover", void 0);
_ts_decorate([
    decorators_js.property({
        type: Number
    })
], DotLottiePlayer.prototype, "intermission", void 0);
_ts_decorate([
    decorators_js.property({
        type: Boolean,
        reflect: true
    })
], DotLottiePlayer.prototype, "loop", void 0);
_ts_decorate([
    decorators_js.property({
        type: String
    })
], DotLottiePlayer.prototype, "mode", void 0);
_ts_decorate([
    decorators_js.property({
        type: Array
    })
], DotLottiePlayer.prototype, "multiAnimationSettings", void 0);
_ts_decorate([
    decorators_js.property({
        type: String
    })
], DotLottiePlayer.prototype, "objectfit", void 0);
_ts_decorate([
    decorators_js.property({
        type: String
    })
], DotLottiePlayer.prototype, "preserveAspectRatio", void 0);
_ts_decorate([
    decorators_js.property({
        type: String
    })
], DotLottiePlayer.prototype, "renderer", void 0);
_ts_decorate([
    decorators_js.property({
        type: Array
    })
], DotLottiePlayer.prototype, "segment", void 0);
_ts_decorate([
    decorators_js.property({
        type: Boolean
    })
], DotLottiePlayer.prototype, "simple", void 0);
_ts_decorate([
    decorators_js.property({
        type: Number
    })
], DotLottiePlayer.prototype, "speed", void 0);
_ts_decorate([
    decorators_js.property({
        type: String
    })
], DotLottiePlayer.prototype, "src", void 0);
_ts_decorate([
    decorators_js.property({
        type: Boolean
    })
], DotLottiePlayer.prototype, "subframe", void 0);
_ts_decorate([
    decorators_js.query('.animation')
], DotLottiePlayer.prototype, "container", void 0);
_ts_decorate([
    decorators_js.state()
], DotLottiePlayer.prototype, "_isSettingsOpen", void 0);
_ts_decorate([
    decorators_js.state()
], DotLottiePlayer.prototype, "_seeker", void 0);
_ts_decorate([
    decorators_js.state()
], DotLottiePlayer.prototype, "_currentAnimation", void 0);
_ts_decorate([
    decorators_js.state()
], DotLottiePlayer.prototype, "_animations", void 0);
DotLottiePlayer = _ts_decorate([
    decorators_js.customElement('dotlottie-player')
], DotLottiePlayer);

globalThis.dotLottiePlayer = ()=>new DotLottiePlayer();

exports.DotLottiePlayer = DotLottiePlayer;
