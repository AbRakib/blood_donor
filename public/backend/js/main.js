// \0rolldown/runtime.js
var e = Object.create, t = Object.defineProperty, n = Object.getOwnPropertyDescriptor, r = Object.getOwnPropertyNames, i = Object.getPrototypeOf, a = Object.prototype.hasOwnProperty, o = (e, t) => () => (t || (e((t = { exports: {} }).exports, t, t.exports), e = null), t.exports), s = (e, i, o, s) => {
	if (i && typeof i == "object" || typeof i == "function") for (var c = r(i), l = 0, u = c.length, d; l < u; l++) d = c[l], !a.call(e, d) && d !== o && t(e, d, {
		get: ((e) => i[e]).bind(null, d),
		enumerable: !(s = n(i, d)) || s.enumerable
	});
	return e;
}, c = (n, r, o) => (o = n == null ? {} : e(i(n)), s(r || !n || !n.__esModule || !a.call(n, "default") ? t(o, "default", {
	value: n,
	enumerable: !0
}) : o, n));
// resources/js/donor-filters.js
function l() {
	let e = [];
	function t(t = null) {
		e.forEach(({ trigger: e, menu: n }) => {
			n !== t && (n.hidden = !0, e.setAttribute("aria-expanded", "false"));
		});
	}
	document.querySelectorAll("#directory .dirtools select").forEach((n) => {
		let r = document.createElement("div");
		r.className = "donor-filter";
		let i = document.createElement("button");
		i.type = "button", i.className = "donor-filter-trigger", i.setAttribute("aria-haspopup", "listbox"), i.setAttribute("aria-expanded", "false");
		let a = document.createElement("span"), o = document.createElement("i");
		o.className = "fi fi-rr-angle-small-down", o.setAttribute("aria-hidden", "true"), i.append(a, o);
		let s = document.createElement("div");
		s.id = `${n.id}-options`, s.className = "donor-filter-menu", s.hidden = !0, s.setAttribute("role", "listbox"), s.setAttribute("aria-label", n.options[0].textContent), i.setAttribute("aria-controls", s.id), n.before(r), r.append(n, i, s), n.hidden = !0, e.push({
			trigger: i,
			menu: s
		});
		function c() {
			a.textContent = n.selectedOptions[0]?.textContent || n.options[0].textContent, i.classList.toggle("is-selected", n.value !== ""), s.replaceChildren(...Array.from(n.options, (e) => {
				let r = document.createElement("button");
				return r.type = "button", r.className = "donor-filter-option", r.setAttribute("role", "option"), r.setAttribute("aria-selected", String(e.selected)), r.textContent = e.textContent, r.disabled = e.disabled, r.tabIndex = -1, r.addEventListener("click", () => {
					n.value = e.value, c(), t(), i.focus(), n.dispatchEvent(new Event("change", { bubbles: !0 }));
				}), r;
			}));
		}
		function l(e = !1) {
			t(s), s.hidden = !1, i.setAttribute("aria-expanded", "true");
			let n = s.querySelectorAll("button:not(:disabled)"), r = s.querySelector("[aria-selected=\"true\"]:not(:disabled)");
			(e ? n[n.length - 1] : r || n[0])?.focus();
		}
		i.addEventListener("click", () => {
			s.hidden ? l() : t();
		}), i.addEventListener("keydown", (e) => {
			["ArrowDown", "ArrowUp"].includes(e.key) && (e.preventDefault(), l(e.key === "ArrowUp"));
		}), r.addEventListener("keydown", (e) => {
			if (e.key === "Escape" && (t(), i.focus()), s.hidden || ![
				"ArrowDown",
				"ArrowUp",
				"Home",
				"End"
			].includes(e.key)) return;
			e.preventDefault();
			let n = Array.from(s.querySelectorAll("button:not(:disabled)")), r = n.indexOf(document.activeElement);
			n[e.key === "Home" ? 0 : e.key === "End" ? n.length - 1 : (r + (e.key === "ArrowDown" ? 1 : -1) + n.length) % n.length]?.focus();
		}), r.addEventListener("focusout", (e) => {
			r.contains(e.relatedTarget) || t();
		}), n.addEventListener("change", c), new MutationObserver(c).observe(n, {
			childList: !0,
			subtree: !0
		}), c();
	}), document.addEventListener("click", (e) => {
		e.target.closest(".donor-filter") || t();
	});
}
// node_modules/sweetalert2/dist/sweetalert2.css
var u = /* @__PURE__ */ c((/* @__PURE__ */ o(((e, t, n) => {
	(function(n, r) {
		typeof e == "object" && t !== void 0 ? t.exports = r() : typeof define == "function" && define.amd ? define(r) : (n = typeof globalThis < "u" ? globalThis : n || self, n.Sweetalert2 = r());
	})(n, (function() {
		function e(e, t, n) {
			if (typeof e == "function" ? e === t : e.has(t)) return arguments.length < 3 ? t : n;
			throw TypeError("Private element is not present on this object");
		}
		function t(e, t) {
			if (t.has(e)) throw TypeError("Cannot initialize the same private elements twice on an object");
		}
		function n(t, n) {
			return t.get(e(t, n));
		}
		function r(e, n, r) {
			t(e, n), n.set(e, r);
		}
		function i(t, n, r) {
			return t.set(e(t, n), r), r;
		}
		let a = {}, o = () => {
			a.previousActiveElement instanceof HTMLElement ? (a.previousActiveElement.focus(), a.previousActiveElement = null) : document.body && document.body.focus();
		}, s = (e) => new Promise((t) => {
			if (!e) return t();
			let n = window.scrollX, r = window.scrollY;
			a.restoreFocusTimeout = setTimeout(() => {
				o(), t();
			}, 100), window.scrollTo(n, r);
		}), c = "swal2-", l = (/* @__PURE__ */ "container.shown.height-auto.iosfix.popup.modal.no-backdrop.no-transition.toast.toast-shown.show.hide.close.title.html-container.actions.confirm.deny.cancel.footer.icon.icon-content.image.input.file.range.select.radio.checkbox.label.textarea.inputerror.input-label.validation-message.progress-steps.active-progress-step.progress-step.progress-step-line.loader.loading.styled.top.top-start.top-end.top-left.top-right.center.center-start.center-end.center-left.center-right.bottom.bottom-start.bottom-end.bottom-left.bottom-right.grow-row.grow-column.grow-fullscreen.rtl.timer-progress-bar.timer-progress-bar-container.scrollbar-measure.icon-success.icon-warning.icon-info.icon-question.icon-error.draggable.dragging".split(".")).reduce((e, t) => (e[t] = c + t, e), {}), u = [
			"success",
			"warning",
			"info",
			"question",
			"error"
		].reduce((e, t) => (e[t] = c + t, e), {}), d = "SweetAlert2:", f = (e) => e.charAt(0).toUpperCase() + e.slice(1), p = (e) => {
			console.warn(`${d} ${typeof e == "object" ? e.join(" ") : e}`);
		}, m = (e) => {
			console.error(`${d} ${e}`);
		}, h = [], ee = (e) => {
			h.includes(e) || (h.push(e), p(e));
		}, g = (e, t = null) => {
			ee(`"${e}" is deprecated and will be removed in the next major release.${t ? ` Use "${t}" instead.` : ""}`);
		}, _ = (e) => typeof e == "function" ? e() : e, te = (e) => e && typeof e.toPromise == "function", v = (e) => te(e) ? e.toPromise() : Promise.resolve(e), ne = (e) => e && Promise.resolve(e) === e, re = () => navigator.userAgent.includes("Firefox"), y = () => document.body.querySelector(`.${l.container}`), b = (e) => {
			let t = y();
			return t ? t.querySelector(e) : null;
		}, x = (e) => b(`.${e}`), S = () => x(l.popup), C = () => x(l.icon), ie = () => x(l["icon-content"]), ae = () => x(l.title), oe = () => x(l["html-container"]), se = () => x(l.image), w = () => x(l["progress-steps"]), ce = () => x(l["validation-message"]), T = () => b(`.${l.actions} .${l.confirm}`), E = () => b(`.${l.actions} .${l.cancel}`), D = () => b(`.${l.actions} .${l.deny}`), O = () => x(l["input-label"]), k = () => b(`.${l.loader}`), A = () => x(l.actions), le = () => x(l.footer), j = () => x(l["timer-progress-bar"]), ue = () => x(l.close), de = () => {
			let e = S();
			if (!e) return [];
			let t = e.querySelectorAll("[tabindex]:not([tabindex=\"-1\"]):not([tabindex=\"0\"])"), n = Array.from(t).sort((e, t) => {
				let n = parseInt(e.getAttribute("tabindex") || "0"), r = parseInt(t.getAttribute("tabindex") || "0");
				return n > r ? 1 : n < r ? -1 : 0;
			}), r = e.querySelectorAll("\n  a[href],\n  area[href],\n  input:not([disabled]),\n  select:not([disabled]),\n  textarea:not([disabled]),\n  button:not([disabled]),\n  iframe,\n  object,\n  embed,\n  [tabindex=\"0\"],\n  [contenteditable],\n  audio[controls],\n  video[controls],\n  summary\n"), i = Array.from(r).filter((e) => e.getAttribute("tabindex") !== "-1");
			return [...new Set(n.concat(i))].filter((e) => U(e));
		}, fe = () => P(document.body, l.shown) && !P(document.body, l["toast-shown"]) && !P(document.body, l["no-backdrop"]), pe = () => {
			let e = S();
			return e ? P(e, l.toast) : !1;
		}, M = () => {
			let e = S();
			return e ? e.hasAttribute("data-loading") : !1;
		}, N = (e, t) => {
			if (e.textContent = "", t) {
				let n = new DOMParser().parseFromString(t, "text/html"), r = n.querySelector("head");
				r && Array.from(r.childNodes).forEach((t) => {
					e.appendChild(t);
				});
				let i = n.querySelector("body");
				i && Array.from(i.childNodes).forEach((t) => {
					t instanceof HTMLVideoElement || t instanceof HTMLAudioElement ? e.appendChild(t.cloneNode(!0)) : e.appendChild(t);
				});
			}
		}, P = (e, t) => t ? t.split(/\s+/).every((t) => e.classList.contains(t)) : !1, me = (e, t) => {
			Array.from(e.classList).forEach((n) => {
				Object.values(l).includes(n) || Object.values(u).includes(n) || Object.values(t.showClass || {}).includes(n) || e.classList.remove(n);
			});
		}, F = (e, t, n) => {
			if (me(e, t), !t.customClass) return;
			let r = t.customClass[n];
			r && (typeof r != "string" && !r.forEach ? p(`Invalid type of customClass.${n}! Expected string or iterable object, got "${typeof r}"`) : L(e, r));
		}, I = (e, t) => {
			if (!t) return null;
			switch (t) {
				case "select":
				case "textarea":
				case "file": return e.querySelector(`.${l.popup} > .${l[t]}`);
				case "checkbox": return e.querySelector(`.${l.popup} > .${l.checkbox} input`);
				case "radio": return e.querySelector(`.${l.popup} > .${l.radio} input:checked`) || e.querySelector(`.${l.popup} > .${l.radio} input:first-child`);
				case "range": return e.querySelector(`.${l.popup} > .${l.range} input`);
				default: return e.querySelector(`.${l.popup} > .${l.input}`);
			}
		}, he = (e) => {
			if (e.focus(), e.type !== "file") {
				let t = e.value;
				e.value = "", e.value = t;
			}
		}, ge = (e, t, n) => {
			if (!e || !t) return;
			let r = typeof t == "string" ? t.split(/\s+/).filter(Boolean) : t;
			(Array.isArray(e) ? e : [e]).forEach((e) => {
				r.forEach((t) => {
					n ? e.classList.add(t) : e.classList.remove(t);
				});
			});
		}, L = (e, t) => {
			ge(e, t, !0);
		}, R = (e, t) => {
			ge(e, t, !1);
		}, z = (e, t) => Array.from(e.children).find((e) => e instanceof HTMLElement && P(e, t)), B = (e, t, n) => {
			n === `${parseInt(`${n}`)}` && (n = parseInt(n)), n || n === 0 ? e.style.setProperty(t, typeof n == "number" ? `${n}px` : n) : e.style.removeProperty(t);
		}, V = (e, t = "flex") => {
			e && (e.style.display = t);
		}, H = (e) => {
			e && (e.style.display = "none");
		}, _e = (e, t = "block") => {
			e && new MutationObserver(() => {
				ye(e, e.innerHTML, t);
			}).observe(e, {
				childList: !0,
				subtree: !0
			});
		}, ve = (e, t, n, r) => {
			let i = e.querySelector(t);
			i && i.style.setProperty(n, r);
		}, ye = (e, t, n = "flex") => {
			t ? V(e, n) : H(e);
		}, U = (e) => !!(e && (e.offsetWidth || e.offsetHeight || e.getClientRects().length)), be = () => !U(T()) && !U(D()) && !U(E()), xe = (e) => e.scrollHeight > e.clientHeight, Se = (e, t) => {
			let n = e;
			for (; n && n !== t;) {
				if (xe(n)) return !0;
				n = n.parentElement;
			}
			return !1;
		}, Ce = (e) => {
			let t = window.getComputedStyle(e), n = parseFloat(t.getPropertyValue("animation-duration") || "0"), r = parseFloat(t.getPropertyValue("transition-duration") || "0");
			return n > 0 || r > 0;
		}, we = (e, t = !1) => {
			let n = j();
			n && U(n) && (t && (n.style.transition = "none", n.style.width = "100%"), setTimeout(() => {
				n.style.transition = `width ${e / 1e3}s linear`, n.style.width = "0%";
			}, 10));
		}, Te = () => {
			let e = j();
			if (!e) return;
			let t = parseInt(window.getComputedStyle(e).width);
			e.style.removeProperty("transition"), e.style.width = "100%";
			let n = t / parseInt(window.getComputedStyle(e).width) * 100;
			e.style.width = `${n}%`;
		}, Ee = () => typeof window > "u" || typeof document > "u", De = `
 <div aria-labelledby="${l.title}" aria-describedby="${l["html-container"]}" class="${l.popup}" tabindex="-1">
   <button type="button" class="${l.close}"></button>
   <ul class="${l["progress-steps"]}"></ul>
   <div class="${l.icon}"></div>
   <img class="${l.image}" />
   <h2 class="${l.title}" id="${l.title}"></h2>
   <div class="${l["html-container"]}" id="${l["html-container"]}"></div>
   <input class="${l.input}" id="${l.input}" />
   <input type="file" class="${l.file}" />
   <div class="${l.range}">
     <input type="range" />
     <output></output>
   </div>
   <select class="${l.select}" id="${l.select}"></select>
   <div class="${l.radio}"></div>
   <label class="${l.checkbox}">
     <input type="checkbox" id="${l.checkbox}" />
     <span class="${l.label}"></span>
   </label>
   <textarea class="${l.textarea}" id="${l.textarea}"></textarea>
   <div class="${l["validation-message"]}" id="${l["validation-message"]}"></div>
   <div class="${l.actions}">
     <div class="${l.loader}"></div>
     <button type="button" class="${l.confirm}"></button>
     <button type="button" class="${l.deny}"></button>
     <button type="button" class="${l.cancel}"></button>
   </div>
   <div class="${l.footer}"></div>
   <div class="${l["timer-progress-bar-container"]}">
     <div class="${l["timer-progress-bar"]}"></div>
   </div>
 </div>
`.replace(/(^|\n)\s*/g, ""), Oe = () => {
			let e = y();
			return e ? (e.remove(), R([document.documentElement, document.body], [
				l["no-backdrop"],
				l["toast-shown"],
				l["has-column"]
			]), !0) : !1;
		}, W = () => {
			a.currentInstance && a.currentInstance.resetValidationMessage();
		}, ke = () => {
			let e = S();
			if (!e) return;
			let t = z(e, l.input), n = z(e, l.file), r = e.querySelector(`.${l.range} input`), i = e.querySelector(`.${l.range} output`), a = z(e, l.select), o = e.querySelector(`.${l.checkbox} input`), s = z(e, l.textarea);
			t && (t.oninput = W), n && (n.onchange = W), a && (a.onchange = W), o && (o.onchange = W), s && (s.oninput = W), r && i && (r.oninput = () => {
				W(), i.value = r.value;
			}, r.onchange = () => {
				W(), i.value = r.value;
			});
		}, Ae = (e) => {
			if (typeof e == "string") {
				let t = document.querySelector(e);
				if (!t) throw Error(`Target element "${e}" not found`);
				return t;
			}
			return e;
		}, je = (e) => {
			let t = S();
			t && (t.setAttribute("role", e.toast ? "alert" : "dialog"), t.setAttribute("aria-live", e.toast ? "polite" : "assertive"), e.toast || t.setAttribute("aria-modal", "true"));
		}, Me = (e) => {
			window.getComputedStyle(e).direction === "rtl" && (L(y(), l.rtl), a.isRTL = !0);
		}, Ne = (e) => {
			let t = Oe();
			if (Ee()) {
				m("SweetAlert2 requires document to initialize");
				return;
			}
			let n = document.createElement("div");
			n.className = l.container, t && L(n, l["no-transition"]), N(n, De), n.dataset.swal2Theme = e.theme;
			let r = Ae(e.target || "body");
			r.appendChild(n), e.topLayer && (n.setAttribute("popover", ""), n.showPopover()), je(e), Me(r), ke();
		}, Pe = (e, t) => {
			e instanceof HTMLElement ? t.appendChild(e) : typeof e == "object" ? Fe(e, t) : e && N(t, e);
		}, Fe = (e, t) => {
			"jquery" in e ? Ie(t, e) : N(t, e.toString());
		}, Ie = (e, t) => {
			if (e.textContent = "", 0 in t) for (let n = 0; n in t; n++) e.appendChild(t[n].cloneNode(!0));
			else e.appendChild(t.cloneNode(!0));
		}, Le = (e, t) => {
			let n = A(), r = k();
			n && r && (!t.showConfirmButton && !t.showDenyButton && !t.showCancelButton ? H(n) : V(n), F(n, t, "actions"), Re(n, r, t), N(r, t.loaderHtml || ""), F(r, t, "loader"));
		};
		function Re(e, t, n) {
			let r = T(), i = D(), a = E();
			r && i && a && (Ve(r, "confirm", n), Ve(i, "deny", n), Ve(a, "cancel", n), ze(r, i, a, n), n.reverseButtons && (n.toast ? (e.insertBefore(a, r), e.insertBefore(i, r)) : (e.insertBefore(a, t), e.insertBefore(i, t), e.insertBefore(r, t))));
		}
		function ze(e, t, n, r) {
			r.buttonsStyling ? (L([
				e,
				t,
				n
			], l.styled), [
				[
					e,
					"confirm",
					r.confirmButtonColor
				],
				[
					t,
					"deny",
					r.denyButtonColor
				],
				[
					n,
					"cancel",
					r.cancelButtonColor
				]
			].forEach(([e, t, n]) => {
				n && e.style.setProperty(`--swal2-${t}-button-background-color`, n), Be(e);
			})) : R([
				e,
				t,
				n
			], l.styled);
		}
		function Be(e) {
			let t = window.getComputedStyle(e);
			if (t.getPropertyValue("--swal2-action-button-focus-box-shadow")) return;
			let n = t.backgroundColor.replace(/rgba?\((\d+), (\d+), (\d+).*/, "rgba($1, $2, $3, 0.5)");
			e.style.setProperty("--swal2-action-button-focus-box-shadow", t.getPropertyValue("--swal2-outline").replace(/ rgba\(.*/, ` ${n}`));
		}
		function Ve(e, t, n) {
			let r = f(t);
			ye(e, n[`show${r}Button`], "inline-block"), N(e, n[`${t}ButtonText`] || ""), e.setAttribute("aria-label", n[`${t}ButtonAriaLabel`] || ""), e.className = l[t], F(e, n, `${t}Button`);
		}
		let He = (e, t) => {
			let n = ue();
			n && (N(n, t.closeButtonHtml || ""), F(n, t, "closeButton"), ye(n, t.showCloseButton), n.setAttribute("aria-label", t.closeButtonAriaLabel || ""));
		}, Ue = (e, t) => {
			let n = y();
			n && (We(n, t.backdrop), Ge(n, t.position), Ke(n, t.grow), F(n, t, "container"));
		};
		function We(e, t) {
			typeof t == "string" ? e.style.background = t : t || L([document.documentElement, document.body], l["no-backdrop"]);
		}
		function Ge(e, t) {
			t && (t in l ? L(e, l[t]) : (p("The \"position\" parameter is not valid, defaulting to \"center\""), L(e, l.center)));
		}
		function Ke(e, t) {
			t && L(e, l[`grow-${t}`]);
		}
		var G = {
			innerParams: /* @__PURE__ */ new WeakMap(),
			domCache: /* @__PURE__ */ new WeakMap(),
			focusedElement: /* @__PURE__ */ new WeakMap()
		};
		let qe = [
			"input",
			"file",
			"range",
			"select",
			"radio",
			"checkbox",
			"textarea"
		], Je = (e, t) => {
			let n = S();
			if (!n) return;
			let r = G.innerParams.get(e), i = !r || t.input !== r.input;
			qe.forEach((e) => {
				let r = z(n, l[e]);
				r && (Ze(e, t.inputAttributes), r.className = l[e], i && H(r));
			}), t.input && (i && Ye(t), Qe(t));
		}, Ye = (e) => {
			if (!e.input) return;
			if (!K[e.input]) {
				m(`Unexpected type of input! Expected ${Object.keys(K).join(" | ")}, got "${e.input}"`);
				return;
			}
			let t = tt(e.input);
			if (!t) return;
			let n = K[e.input](t, e);
			V(t), e.inputAutoFocus && setTimeout(() => {
				he(n);
			});
		}, Xe = (e) => {
			for (let { name: t } of Array.from(e.attributes)) [
				"id",
				"type",
				"value",
				"style"
			].includes(t) || e.removeAttribute(t);
		}, Ze = (e, t) => {
			let n = S();
			if (!n) return;
			let r = I(n, e);
			if (r) {
				Xe(r);
				for (let e in t) r.setAttribute(e, t[e]);
			}
		}, Qe = (e) => {
			if (!e.input) return;
			let t = tt(e.input);
			t && F(t, e, "input");
		}, $e = (e, t) => {
			!e.placeholder && t.inputPlaceholder && (e.placeholder = t.inputPlaceholder);
		}, et = (e, t, n) => {
			if (n.inputLabel) {
				let r = document.createElement("label"), i = l["input-label"];
				r.setAttribute("for", e.id), r.className = i, typeof n.customClass == "object" && L(r, n.customClass.inputLabel), r.innerText = n.inputLabel, t.insertAdjacentElement("beforebegin", r);
			}
		}, tt = (e) => {
			let t = S();
			if (t) return z(t, l[e] || l.input);
		}, nt = (e, t) => {
			["string", "number"].includes(typeof t) ? e.value = `${t}` : ne(t) || p(`Unexpected type of inputValue! Expected "string", "number" or "Promise", got "${typeof t}"`);
		}, K = {};
		K.text = K.email = K.password = K.number = K.tel = K.url = K.search = K.date = K["datetime-local"] = K.time = K.week = K.month = (e, t) => {
			let n = e;
			return nt(n, t.inputValue), et(n, n, t), $e(n, t), n.type = t.input, n;
		}, K.file = (e, t) => {
			let n = e;
			return et(n, n, t), $e(n, t), n;
		}, K.range = (e, t) => {
			let n = e, r = n.querySelector("input"), i = n.querySelector("output");
			return r && (nt(r, t.inputValue), r.type = t.input, et(r, e, t)), i && nt(i, t.inputValue), e;
		}, K.select = (e, t) => {
			let n = e;
			if (n.textContent = "", t.inputPlaceholder) {
				let e = document.createElement("option");
				N(e, t.inputPlaceholder), e.value = "", e.disabled = !0, e.selected = !0, n.appendChild(e);
			}
			return et(n, n, t), n;
		}, K.radio = (e) => {
			let t = e;
			return t.textContent = "", e;
		}, K.checkbox = (e, t) => {
			let n = S();
			if (!n) throw Error("Popup not found");
			let r = I(n, "checkbox");
			if (!r) throw Error("Checkbox input not found");
			r.value = "1", r.checked = !!t.inputValue;
			let i = e.querySelector("span");
			if (i) {
				let e = t.inputPlaceholder || t.inputLabel;
				e && N(i, e);
			}
			return r;
		}, K.textarea = (e, t) => {
			let n = e;
			nt(n, t.inputValue), $e(n, t), et(n, n, t);
			let r = (e) => parseInt(window.getComputedStyle(e).marginLeft) + parseInt(window.getComputedStyle(e).marginRight);
			return setTimeout(() => {
				if ("MutationObserver" in window) {
					let e = S();
					if (!e) return;
					let i = parseInt(window.getComputedStyle(e).width);
					new MutationObserver(() => {
						if (!document.body.contains(n)) return;
						let e = n.offsetWidth + r(n), a = S();
						a && (e > i ? a.style.width = `${e}px` : B(a, "width", t.width));
					}).observe(n, {
						attributes: !0,
						attributeFilter: ["style"]
					});
				}
			}), n;
		};
		let rt = (e, t) => {
			let n = oe();
			n && (_e(n), F(n, t, "htmlContainer"), t.html ? (Pe(t.html, n), V(n, "block")) : t.text ? (n.textContent = t.text, V(n, "block")) : H(n), Je(e, t));
		}, it = (e, t) => {
			let n = le();
			n && (_e(n), ye(n, !!t.footer, "block"), t.footer && Pe(t.footer, n), F(n, t, "footer"));
		}, at = (e, t) => {
			let n = G.innerParams.get(e), r = C();
			r && (n && t.icon === n.icon ? (lt(r, t), ot(r, t)) : !t.icon && !t.iconHtml ? H(r) : t.icon && Object.keys(u).indexOf(t.icon) === -1 ? (m(`Unknown icon! Expected "success", "error", "warning", "info" or "question", got "${t.icon}"`), H(r)) : (V(r), lt(r, t), ot(r, t), L(r, t.showClass && t.showClass.icon), window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", st)));
		}, ot = (e, t) => {
			for (let [n, r] of Object.entries(u)) t.icon !== n && R(e, r);
			L(e, t.icon && u[t.icon]), ut(e, t), st(), F(e, t, "icon");
		}, st = () => {
			let e = S();
			if (!e) return;
			let t = window.getComputedStyle(e).getPropertyValue("background-color");
			e.querySelectorAll("[class^=swal2-success-circular-line], .swal2-success-fix").forEach((e) => {
				e.style.backgroundColor = t;
			});
		}, ct = (e) => `
  ${e.animation ? "<div class=\"swal2-success-circular-line-left\"></div>" : ""}
  <span class="swal2-success-line-tip"></span> <span class="swal2-success-line-long"></span>
  <div class="swal2-success-ring"></div>
  ${e.animation ? "<div class=\"swal2-success-fix\"></div>" : ""}
  ${e.animation ? "<div class=\"swal2-success-circular-line-right\"></div>" : ""}
`, lt = (e, t) => {
			if (!t.icon && !t.iconHtml) return;
			let n = e.innerHTML, r = "";
			t.iconHtml ? r = dt(t.iconHtml) : t.icon === "success" ? (r = ct(t), n = n.replace(/ style=".*?"/g, "")) : t.icon === "error" ? r = "\n  <span class=\"swal2-x-mark\">\n    <span class=\"swal2-x-mark-line-left\"></span>\n    <span class=\"swal2-x-mark-line-right\"></span>\n  </span>\n" : t.icon && (r = dt({
				question: "?",
				warning: "!",
				info: "i"
			}[t.icon])), n.trim() !== r.trim() && N(e, r);
		}, ut = (e, t) => {
			if (t.iconColor) {
				e.style.color = t.iconColor, e.style.borderColor = t.iconColor;
				for (let n of [
					".swal2-success-line-tip",
					".swal2-success-line-long",
					".swal2-x-mark-line-left",
					".swal2-x-mark-line-right"
				]) ve(e, n, "background-color", t.iconColor);
				ve(e, ".swal2-success-ring", "border-color", t.iconColor);
			}
		}, dt = (e) => `<div class="${l["icon-content"]}">${e}</div>`, ft = (e, t) => {
			let n = se();
			n && (t.imageUrl ? (V(n, ""), n.setAttribute("src", t.imageUrl), n.setAttribute("alt", t.imageAlt || ""), B(n, "width", t.imageWidth), B(n, "height", t.imageHeight), n.className = l.image, F(n, t, "image")) : H(n));
		}, pt = !1, mt = 0, ht = 0, gt = 0, _t = 0, vt = (e) => {
			e.addEventListener("mousedown", bt), document.body.addEventListener("mousemove", xt), e.addEventListener("mouseup", St), e.addEventListener("touchstart", bt), document.body.addEventListener("touchmove", xt), e.addEventListener("touchend", St);
		}, yt = (e) => {
			e.removeEventListener("mousedown", bt), document.body.removeEventListener("mousemove", xt), e.removeEventListener("mouseup", St), e.removeEventListener("touchstart", bt), document.body.removeEventListener("touchmove", xt), e.removeEventListener("touchend", St);
		}, bt = (e) => {
			let t = S();
			if (!t) return;
			let n = C();
			if (e.target === t || n && n.contains(e.target)) {
				pt = !0;
				let n = Ct(e);
				mt = n.clientX, ht = n.clientY, gt = parseInt(t.style.insetInlineStart) || 0, _t = parseInt(t.style.insetBlockStart) || 0, L(t, "swal2-dragging");
			}
		}, xt = (e) => {
			let t = S();
			if (t && pt) {
				let { clientX: n, clientY: r } = Ct(e), i = n - mt;
				t.style.insetInlineStart = `${gt + (a.isRTL ? -i : i)}px`, t.style.insetBlockStart = `${_t + (r - ht)}px`;
			}
		}, St = () => {
			let e = S();
			pt = !1, R(e, "swal2-dragging");
		}, Ct = (e) => {
			let t = e.type.startsWith("touch") ? e.touches[0] : e;
			return {
				clientX: t.clientX,
				clientY: t.clientY
			};
		}, wt = (e, t) => {
			let n = y(), r = S();
			if (n && r) {
				if (t.toast) {
					B(n, "width", t.width), r.style.width = "100%";
					let e = k();
					e && r.insertBefore(e, C());
				} else B(r, "width", t.width);
				B(r, "padding", t.padding), t.color && (r.style.color = t.color), t.background && (r.style.background = t.background), H(ce()), Tt(r, t), t.draggable && !t.toast ? (L(r, l.draggable), vt(r)) : (R(r, l.draggable), yt(r));
			}
		}, Tt = (e, t) => {
			let n = t.showClass || {};
			e.className = `${l.popup} ${U(e) ? n.popup : ""}`, t.toast ? (L([document.documentElement, document.body], l["toast-shown"]), L(e, l.toast)) : L(e, l.modal), F(e, t, "popup"), typeof t.customClass == "string" && L(e, t.customClass), t.icon && L(e, l[`icon-${t.icon}`]);
		}, Et = (e, t) => {
			let n = w();
			if (!n) return;
			let { progressSteps: r, currentProgressStep: i } = t;
			!r || r.length === 0 || i === void 0 ? H(n) : (V(n), n.textContent = "", i >= r.length && p("Invalid currentProgressStep parameter, it should be less than progressSteps.length (currentProgressStep like JS arrays starts from 0)"), r.forEach((e, a) => {
				let o = Dt(e);
				if (n.appendChild(o), a === i && L(o, l["active-progress-step"]), a !== r.length - 1) {
					let e = Ot(t);
					n.appendChild(e);
				}
			}));
		}, Dt = (e) => {
			let t = document.createElement("li");
			return L(t, l["progress-step"]), N(t, e), t;
		}, Ot = (e) => {
			let t = document.createElement("li");
			return L(t, l["progress-step-line"]), e.progressStepsDistance && B(t, "width", e.progressStepsDistance), t;
		}, kt = (e, t) => {
			let n = ae();
			n && (_e(n), ye(n, !!(t.title || t.titleText), "block"), t.title && Pe(t.title, n), t.titleText && (n.innerText = t.titleText), F(n, t, "title"));
		}, At = (e, t) => {
			var n;
			wt(e, t), Ue(e, t), Et(e, t), at(e, t), ft(e, t), kt(e, t), He(e, t), rt(e, t), Le(e, t), it(e, t);
			let r = S();
			typeof t.didRender == "function" && r && t.didRender(r), (n = a.eventEmitter) == null || n.emit("didRender", r);
		}, jt = () => U(S()), Mt = () => T()?.click(), Nt = () => D()?.click(), Pt = () => E()?.click(), Ft = Object.freeze({
			cancel: "cancel",
			backdrop: "backdrop",
			close: "close",
			esc: "esc",
			timer: "timer"
		}), It = (e) => {
			if (e.keydownTarget && e.keydownHandlerAdded && e.keydownHandler) {
				let t = e.keydownHandler;
				e.keydownTarget.removeEventListener("keydown", t, { capture: e.keydownListenerCapture }), e.keydownHandlerAdded = !1;
			}
		}, Lt = (e, t, n) => {
			if (It(e), !t.toast) {
				let r = (e) => Vt(t, e, n);
				e.keydownHandler = r;
				let i = t.keydownListenerCapture ? window : S();
				if (i) {
					e.keydownTarget = i, e.keydownListenerCapture = t.keydownListenerCapture;
					let n = r;
					e.keydownTarget.addEventListener("keydown", n, { capture: e.keydownListenerCapture }), e.keydownHandlerAdded = !0;
				}
			}
		}, Rt = (e, t) => {
			var n;
			let r = de();
			return r.length ? (e += t, e === -2 && (e = r.length - 1), e === r.length ? e = 0 : e === -1 && (e = r.length - 1), r[e].focus(), !(re() && r[e] instanceof HTMLIFrameElement)) : ((n = S()) == null || n.focus(), !0);
		}, zt = ["ArrowRight", "ArrowDown"], Bt = ["ArrowLeft", "ArrowUp"], Vt = (e, t, n) => {
			e && (t.isComposing || t.keyCode === 229 || (e.stopKeydownPropagation && t.stopPropagation(), t.key === "Enter" ? Ht(t, e) : t.key === "Tab" ? Ut(t) : [...zt, ...Bt].includes(t.key) ? Wt(t.key) : t.key === "Escape" && Gt(t, e, n)));
		}, Ht = (e, t) => {
			if (!_(t.allowEnterKey)) return;
			let n = S();
			if (!n || !t.input) return;
			let r = I(n, t.input);
			if (e.target && r && e.target instanceof HTMLElement && e.target.outerHTML === r.outerHTML) {
				if (["textarea", "file"].includes(t.input)) return;
				Mt(), e.preventDefault();
			}
		}, Ut = (e) => {
			let t = e.target, n = de().findIndex((e) => e === t), r = !0;
			r = e.shiftKey ? Rt(n, -1) : Rt(n, 1), e.stopPropagation(), r && e.preventDefault();
		}, Wt = (e) => {
			let t = A(), n = T(), r = D(), i = E();
			if (!t || !n || !r || !i) return;
			let a = [
				n,
				r,
				i
			];
			if (document.activeElement instanceof HTMLElement && !a.includes(document.activeElement)) return;
			let o = zt.includes(e) ? "nextElementSibling" : "previousElementSibling", s = document.activeElement;
			if (s) {
				for (let e = 0; e < t.children.length; e++) {
					if (s = s[o], !s) return;
					if (s instanceof HTMLButtonElement && U(s)) break;
				}
				s instanceof HTMLButtonElement && s.focus();
			}
		}, Gt = (e, t, n) => {
			e.preventDefault(), _(t.allowEscapeKey) && n(Ft.esc);
		};
		var q = {
			swalPromiseResolve: /* @__PURE__ */ new WeakMap(),
			swalPromiseReject: /* @__PURE__ */ new WeakMap()
		};
		let Kt = () => {
			let e = y();
			Array.from(document.body.children).forEach((t) => {
				t.contains(e) || (t.hasAttribute("aria-hidden") && t.setAttribute("data-previous-aria-hidden", t.getAttribute("aria-hidden") || ""), t.setAttribute("aria-hidden", "true"));
			});
		}, qt = () => {
			Array.from(document.body.children).forEach((e) => {
				e.hasAttribute("data-previous-aria-hidden") ? (e.setAttribute("aria-hidden", e.getAttribute("data-previous-aria-hidden") || ""), e.removeAttribute("data-previous-aria-hidden")) : e.removeAttribute("aria-hidden");
			});
		}, Jt = typeof window < "u" && !!window.GestureEvent, Yt = Jt && /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream, Xt = () => {
			if (Jt && !P(document.body, l.iosfix)) {
				let e = document.body.scrollTop;
				document.body.style.top = `${e * -1}px`, L(document.body, l.iosfix), Zt();
			}
		}, Zt = () => {
			let e = y();
			if (!e) return;
			let t;
			e.ontouchstart = (e) => {
				t = Qt(e);
			}, e.ontouchmove = (e) => {
				t && (e.preventDefault(), e.stopPropagation());
			};
		}, Qt = (e) => {
			let t = e.target, n = y(), r = oe();
			return !n || !r || $t(e) || en(e) ? !1 : t === n || !xe(n) && t instanceof HTMLElement && !Se(t, r) && t.tagName !== "INPUT" && t.tagName !== "TEXTAREA" && !(xe(r) && r.contains(t));
		}, $t = (e) => !!(e.touches && e.touches.length && e.touches[0].touchType === "stylus"), en = (e) => e.touches && e.touches.length > 1, tn = () => {
			if (P(document.body, l.iosfix)) {
				let e = parseInt(document.body.style.top, 10);
				R(document.body, l.iosfix), document.body.style.top = "", document.body.scrollTop = e * -1;
			}
		}, nn = () => {
			let e = document.createElement("div");
			e.className = l["scrollbar-measure"], document.body.appendChild(e);
			let t = e.getBoundingClientRect().width - e.clientWidth;
			return document.body.removeChild(e), t;
		}, rn = null, an = (e) => {
			rn === null && (document.body.scrollHeight > window.innerHeight || e === "scroll") && (rn = parseInt(window.getComputedStyle(document.body).getPropertyValue("padding-right")), document.body.style.paddingRight = `${rn + nn()}px`);
		}, on = () => {
			rn !== null && (document.body.style.paddingRight = `${rn}px`, rn = null);
		};
		function sn(e, t, n, r) {
			pe() ? hn(e, r) : (s(n).then(() => hn(e, r)), It(a)), Jt ? (t.setAttribute("style", "display:none !important"), t.removeAttribute("class"), t.innerHTML = "") : t.remove(), fe() && (on(), tn(), qt()), cn();
		}
		function cn() {
			R([document.documentElement, document.body], [
				l.shown,
				l["height-auto"],
				l["no-backdrop"],
				l["toast-shown"]
			]);
		}
		function J(e) {
			e = fn(e);
			let t = q.swalPromiseResolve.get(this), n = ln(this);
			this.isAwaitingPromise ? e.isDismissed || (dn(this), t(e)) : n && t(e);
		}
		let ln = (e) => {
			let t = S();
			if (!t) return !1;
			let n = G.innerParams.get(e);
			if (!n || P(t, n.hideClass.popup)) return !1;
			R(t, n.showClass.popup), L(t, n.hideClass.popup);
			let r = y();
			return R(r, n.showClass.backdrop), L(r, n.hideClass.backdrop), pn(e, t, n), !0;
		};
		function un(e) {
			let t = q.swalPromiseReject.get(this);
			dn(this), t && t(e);
		}
		let dn = (e) => {
			e.isAwaitingPromise && (delete e.isAwaitingPromise, G.innerParams.get(e) || e._destroy());
		}, fn = (e) => e === void 0 ? {
			isConfirmed: !1,
			isDenied: !1,
			isDismissed: !0
		} : Object.assign({
			isConfirmed: !1,
			isDenied: !1,
			isDismissed: !1
		}, e), pn = (e, t, n) => {
			var r;
			let i = y(), o = Ce(t);
			typeof n.willClose == "function" && n.willClose(t), (r = a.eventEmitter) == null || r.emit("willClose", t), o && i ? mn(e, t, i, !!n.returnFocus, n.didClose) : i && sn(e, i, !!n.returnFocus, n.didClose);
		}, mn = (e, t, n, r, i) => {
			a.swalCloseEventFinishedCallback = sn.bind(null, e, n, r, i);
			let o = function(e) {
				if (e.target === t) {
					var n;
					(n = a.swalCloseEventFinishedCallback) == null || n.call(a), delete a.swalCloseEventFinishedCallback, t.removeEventListener("animationend", o), t.removeEventListener("transitionend", o);
				}
			};
			t.addEventListener("animationend", o), t.addEventListener("transitionend", o);
		}, hn = (e, t) => {
			setTimeout(() => {
				var n;
				typeof t == "function" && t.bind(e.params)(), (n = a.eventEmitter) == null || n.emit("didClose"), e._destroy && e._destroy();
			});
		}, Y = (e) => {
			let t = S();
			if (t || new li(), t = S(), !t) return;
			let n = k();
			pe() ? H(C()) : gn(t, e), V(n), t.setAttribute("data-loading", "true"), t.setAttribute("aria-busy", "true"), t.focus();
		}, gn = (e, t) => {
			let n = A(), r = k();
			n && r && (!t && U(T()) && (t = T()), V(n), t && (H(t), r.setAttribute("data-button-to-replace", t.className), n.insertBefore(r, t)), L([e, n], l.loading));
		}, _n = (e, t) => {
			t.input === "select" || t.input === "radio" ? Sn(e, t) : [
				"text",
				"email",
				"number",
				"tel",
				"textarea"
			].some((e) => e === t.input) && (te(t.inputValue) || ne(t.inputValue)) && (Y(T()), Cn(e, t));
		}, vn = (e, t) => {
			let n = e.getInput();
			if (!n) return null;
			switch (t.input) {
				case "checkbox": return yn(n);
				case "radio": return bn(n);
				case "file": return xn(n);
				default: return t.inputAutoTrim ? n.value.trim() : n.value;
			}
		}, yn = (e) => +!!e.checked, bn = (e) => e.checked ? e.value : null, xn = (e) => e.files && e.files.length ? e.getAttribute("multiple") === null ? e.files[0] : e.files : null, Sn = (e, t) => {
			let n = S();
			if (!n) return;
			let r = (e) => {
				t.input === "select" ? wn(n, En(e), t) : t.input === "radio" && Tn(n, En(e), t);
			};
			te(t.inputOptions) || ne(t.inputOptions) ? (Y(T()), v(t.inputOptions).then((t) => {
				e.hideLoading(), r(t);
			})) : typeof t.inputOptions == "object" ? r(t.inputOptions) : m(`Unexpected type of inputOptions! Expected object, Map or Promise, got ${typeof t.inputOptions}`);
		}, Cn = (e, t) => {
			let n = e.getInput();
			n && (H(n), v(t.inputValue).then((r) => {
				n.value = t.input === "number" ? `${parseFloat(r) || 0}` : `${r}`, V(n), n.focus(), e.hideLoading();
			}).catch((t) => {
				m(`Error in inputValue promise: ${t}`), n.value = "", V(n), n.focus(), e.hideLoading();
			}));
		};
		function wn(e, t, n) {
			let r = z(e, l.select);
			if (!r) return;
			let i = (e, t, r) => {
				let i = document.createElement("option");
				i.value = r, N(i, t), i.selected = Dn(r, n.inputValue), e.appendChild(i);
			};
			t.forEach((e) => {
				let t = e[0], n = e[1];
				if (Array.isArray(n)) {
					let e = document.createElement("optgroup");
					e.label = t, e.disabled = !1, r.appendChild(e), n.forEach((t) => i(e, t[1], t[0]));
				} else i(r, n, t);
			}), r.focus();
		}
		function Tn(e, t, n) {
			let r = z(e, l.radio);
			if (!r) return;
			t.forEach((e) => {
				let t = e[0], i = e[1], a = document.createElement("input"), o = document.createElement("label");
				a.type = "radio", a.name = l.radio, a.value = t, Dn(t, n.inputValue) && (a.checked = !0);
				let s = document.createElement("span");
				N(s, i), s.className = l.label, o.appendChild(a), o.appendChild(s), r.appendChild(o);
			});
			let i = r.querySelectorAll("input");
			i.length && i[0].focus();
		}
		let En = (e) => (e instanceof Map ? Array.from(e) : Object.entries(e)).map(([e, t]) => [e, typeof t == "object" ? En(t) : t]), Dn = (e, t) => !!t && t != null && t.toString() === e.toString(), On = (e) => {
			let t = G.innerParams.get(e);
			e.disableButtons(), t.input ? jn(e, "confirm") : In(e, !0);
		}, kn = (e) => {
			let t = G.innerParams.get(e);
			e.disableButtons(), t.returnInputValueOnDeny ? jn(e, "deny") : Nn(e, !1);
		}, An = (e, t) => {
			e.disableButtons(), t(Ft.cancel);
		}, jn = (e, t) => {
			let n = G.innerParams.get(e);
			if (!n.input) {
				m(`The "input" parameter is needed to be set when using returnInputValueOn${f(t)}`);
				return;
			}
			let r = e.getInput(), i = vn(e, n);
			n.inputValidator ? Mn(e, i, t) : r && !r.checkValidity() ? (e.enableButtons(), e.showValidationMessage(n.validationMessage || r.validationMessage)) : t === "deny" ? Nn(e, i) : In(e, i);
		}, Mn = (e, t, n) => {
			let r = G.innerParams.get(e);
			e.disableInput(), Promise.resolve().then(() => v(r.inputValidator(t, r.validationMessage))).then((r) => {
				e.enableButtons(), e.enableInput(), r ? e.showValidationMessage(r) : n === "deny" ? Nn(e, t) : In(e, t);
			});
		}, Nn = (e, t) => {
			let n = G.innerParams.get(e);
			n.showLoaderOnDeny && Y(D()), n.preDeny ? (e.isAwaitingPromise = !0, Promise.resolve().then(() => v(n.preDeny(t, n.validationMessage))).then((n) => {
				n === !1 ? (e.hideLoading(), dn(e)) : e.close({
					isDenied: !0,
					value: n === void 0 ? t : n
				});
			}).catch((t) => Fn(e, t))) : e.close({
				isDenied: !0,
				value: t
			});
		}, Pn = (e, t) => {
			e.close({
				isConfirmed: !0,
				value: t
			});
		}, Fn = (e, t) => {
			e.rejectPromise(t);
		}, In = (e, t) => {
			let n = G.innerParams.get(e);
			n.showLoaderOnConfirm && Y(), n.preConfirm ? (e.resetValidationMessage(), e.isAwaitingPromise = !0, Promise.resolve().then(() => v(n.preConfirm(t, n.validationMessage))).then((n) => {
				U(ce()) || n === !1 ? (e.hideLoading(), dn(e)) : Pn(e, n === void 0 ? t : n);
			}).catch((t) => Fn(e, t))) : Pn(e, t);
		};
		function Ln() {
			let e = G.innerParams.get(this);
			if (!e) return;
			let t = G.domCache.get(this);
			H(t.loader), pe() ? e.icon && V(C()) : Rn(t), R([t.popup, t.actions], l.loading), t.popup.removeAttribute("aria-busy"), t.popup.removeAttribute("data-loading"), this.enableButtons();
		}
		let Rn = (e) => {
			let t = e.loader.getAttribute("data-button-to-replace"), n = t ? e.popup.getElementsByClassName(t) : [];
			n.length ? V(n[0], "inline-block") : be() && H(e.actions);
		};
		function zn() {
			let e = G.innerParams.get(this), t = G.domCache.get(this);
			return t ? I(t.popup, e.input) : null;
		}
		function Bn(e, t, n) {
			let r = G.domCache.get(e);
			t.forEach((e) => {
				r[e].disabled = n;
			});
		}
		function Vn(e, t) {
			let n = S();
			n && e && (e.type === "radio" ? n.querySelectorAll(`[name="${l.radio}"]`).forEach((e) => {
				e.disabled = t;
			}) : e.disabled = t);
		}
		function Hn() {
			Bn(this, [
				"confirmButton",
				"denyButton",
				"cancelButton"
			], !1);
			let e = G.focusedElement.get(this);
			e instanceof HTMLElement && document.activeElement === document.body && e.focus(), G.focusedElement.delete(this);
		}
		function Un() {
			G.focusedElement.set(this, document.activeElement), Bn(this, [
				"confirmButton",
				"denyButton",
				"cancelButton"
			], !0);
		}
		function Wn() {
			Vn(this.getInput(), !1);
		}
		function Gn() {
			Vn(this.getInput(), !0);
		}
		function Kn(e) {
			let t = G.domCache.get(this), n = G.innerParams.get(this);
			N(t.validationMessage, e), t.validationMessage.className = l["validation-message"], n.customClass && n.customClass.validationMessage && L(t.validationMessage, n.customClass.validationMessage), V(t.validationMessage);
			let r = this.getInput();
			r && (r.setAttribute("aria-invalid", "true"), r.setAttribute("aria-describedby", l["validation-message"]), he(r), L(r, l.inputerror));
		}
		function qn() {
			let e = G.domCache.get(this);
			e.validationMessage && H(e.validationMessage);
			let t = this.getInput();
			t && (t.removeAttribute("aria-invalid"), t.removeAttribute("aria-describedby"), R(t, l.inputerror));
		}
		let X = {
			title: "",
			titleText: "",
			text: "",
			html: "",
			footer: "",
			icon: void 0,
			iconColor: void 0,
			iconHtml: void 0,
			template: void 0,
			toast: !1,
			draggable: !1,
			animation: !0,
			theme: "light",
			showClass: {
				popup: "swal2-show",
				backdrop: "swal2-backdrop-show",
				icon: "swal2-icon-show"
			},
			hideClass: {
				popup: "swal2-hide",
				backdrop: "swal2-backdrop-hide",
				icon: "swal2-icon-hide"
			},
			customClass: {},
			target: "body",
			color: void 0,
			backdrop: !0,
			heightAuto: !0,
			allowOutsideClick: !0,
			allowEscapeKey: !0,
			allowEnterKey: !0,
			stopKeydownPropagation: !0,
			keydownListenerCapture: !1,
			showConfirmButton: !0,
			showDenyButton: !1,
			showCancelButton: !1,
			preConfirm: void 0,
			preDeny: void 0,
			confirmButtonText: "OK",
			confirmButtonAriaLabel: "",
			confirmButtonColor: void 0,
			denyButtonText: "No",
			denyButtonAriaLabel: "",
			denyButtonColor: void 0,
			cancelButtonText: "Cancel",
			cancelButtonAriaLabel: "",
			cancelButtonColor: void 0,
			buttonsStyling: !0,
			reverseButtons: !1,
			focusConfirm: !0,
			focusDeny: !1,
			focusCancel: !1,
			returnFocus: !0,
			showCloseButton: !1,
			closeButtonHtml: "&times;",
			closeButtonAriaLabel: "Close this dialog",
			loaderHtml: "",
			showLoaderOnConfirm: !1,
			showLoaderOnDeny: !1,
			imageUrl: void 0,
			imageWidth: void 0,
			imageHeight: void 0,
			imageAlt: "",
			timer: void 0,
			timerProgressBar: !1,
			width: void 0,
			padding: void 0,
			background: void 0,
			input: void 0,
			inputPlaceholder: "",
			inputLabel: "",
			inputValue: "",
			inputOptions: {},
			inputAutoFocus: !0,
			inputAutoTrim: !0,
			inputAttributes: {},
			inputValidator: void 0,
			returnInputValueOnDeny: !1,
			validationMessage: void 0,
			grow: !1,
			position: "center",
			progressSteps: [],
			currentProgressStep: void 0,
			progressStepsDistance: void 0,
			willOpen: void 0,
			didOpen: void 0,
			didRender: void 0,
			willClose: void 0,
			didClose: void 0,
			didDestroy: void 0,
			scrollbarPadding: !0,
			topLayer: !1
		}, Jn = /* @__PURE__ */ "allowEscapeKey.allowOutsideClick.background.buttonsStyling.cancelButtonAriaLabel.cancelButtonColor.cancelButtonText.closeButtonAriaLabel.closeButtonHtml.color.confirmButtonAriaLabel.confirmButtonColor.confirmButtonText.currentProgressStep.customClass.denyButtonAriaLabel.denyButtonColor.denyButtonText.didClose.didDestroy.draggable.footer.hideClass.html.icon.iconColor.iconHtml.imageAlt.imageHeight.imageUrl.imageWidth.preConfirm.preDeny.progressSteps.returnFocus.reverseButtons.showCancelButton.showCloseButton.showConfirmButton.showDenyButton.text.title.titleText.theme.willClose".split("."), Yn = { allowEnterKey: void 0 }, Xn = [
			"allowOutsideClick",
			"allowEnterKey",
			"backdrop",
			"draggable",
			"focusConfirm",
			"focusDeny",
			"focusCancel",
			"returnFocus",
			"heightAuto",
			"keydownListenerCapture"
		], Zn = (e) => Object.prototype.hasOwnProperty.call(X, e), Qn = (e) => Jn.indexOf(e) !== -1, $n = (e) => Yn[e], er = (e) => {
			Zn(e) || p(`Unknown parameter "${e}"`);
		}, tr = (e) => {
			Xn.includes(e) && p(`The parameter "${e}" is incompatible with toasts`);
		}, nr = (e) => {
			let t = $n(e);
			t && g(e, t);
		}, rr = (e) => {
			e.backdrop === !1 && e.allowOutsideClick && p("\"allowOutsideClick\" parameter requires `backdrop` parameter to be set to `true`"), e.theme && ![
				"light",
				"dark",
				"auto",
				"minimal",
				"borderless",
				"bootstrap-4",
				"bootstrap-4-light",
				"bootstrap-4-dark",
				"bootstrap-5",
				"bootstrap-5-light",
				"bootstrap-5-dark",
				"material-ui",
				"material-ui-light",
				"material-ui-dark",
				"embed-iframe",
				"bulma",
				"bulma-light",
				"bulma-dark"
			].includes(e.theme) && p(`Invalid theme "${e.theme}"`);
			for (let t in e) er(t), e.toast && tr(t), nr(t);
		};
		function ir(e) {
			let t = y(), n = S(), r = G.innerParams.get(this);
			if (!n || P(n, r.hideClass.popup)) {
				p("You're trying to update the closed or closing popup, that won't work. Use the update() method in preConfirm parameter or show a new popup.");
				return;
			}
			let i = ar(e), a = Object.assign({}, r, i);
			rr(a), t && (t.dataset.swal2Theme = a.theme), At(this, a), G.innerParams.set(this, a), Object.defineProperties(this, { params: {
				value: Object.assign({}, this.params, e),
				writable: !1,
				enumerable: !0
			} });
		}
		let ar = (e) => {
			let t = {};
			return Object.keys(e).forEach((n) => {
				Qn(n) ? t[n] = e[n] : p(`Invalid parameter to update: ${n}`);
			}), t;
		};
		function or() {
			var e;
			let t = G.domCache.get(this), n = G.innerParams.get(this);
			n ? (t.popup && a.swalCloseEventFinishedCallback && (a.swalCloseEventFinishedCallback(), delete a.swalCloseEventFinishedCallback), typeof n.didDestroy == "function" && n.didDestroy(), (e = a.eventEmitter) == null || e.emit("didDestroy"), sr(this)) : cr(this);
		}
		let sr = (e) => {
			cr(e), delete e.params, delete a.keydownHandler, delete a.keydownTarget, delete a.currentInstance;
		}, cr = (e) => {
			e.isAwaitingPromise ? (lr(G, e), e.isAwaitingPromise = !0) : (lr(q, e), lr(G, e), delete e.isAwaitingPromise, delete e.disableButtons, delete e.enableButtons, delete e.getInput, delete e.disableInput, delete e.enableInput, delete e.hideLoading, delete e.disableLoading, delete e.showValidationMessage, delete e.resetValidationMessage, delete e.close, delete e.closePopup, delete e.closeModal, delete e.closeToast, delete e.rejectPromise, delete e.update, delete e._destroy);
		}, lr = (e, t) => {
			for (let n in e) e[n].delete(t);
		};
		var ur = /*#__PURE__*/ Object.freeze({
			__proto__: null,
			_destroy: or,
			close: J,
			closeModal: J,
			closePopup: J,
			closeToast: J,
			disableButtons: Un,
			disableInput: Gn,
			disableLoading: Ln,
			enableButtons: Hn,
			enableInput: Wn,
			getInput: zn,
			handleAwaitingPromise: dn,
			hideLoading: Ln,
			rejectPromise: un,
			resetValidationMessage: qn,
			showValidationMessage: Kn,
			update: ir
		});
		let dr = (e, t, n) => {
			e.toast ? fr(e, t, n) : (hr(t), gr(t), _r(e, t, n));
		}, fr = (e, t, n) => {
			t.popup.onclick = () => {
				e && (pr(e) || e.timer || e.input) || n(Ft.close);
			};
		}, pr = (e) => !!(e.showConfirmButton || e.showDenyButton || e.showCancelButton || e.showCloseButton), mr = !1, hr = (e) => {
			e.popup.onmousedown = () => {
				e.container.onmouseup = function(t) {
					e.container.onmouseup = () => {}, t.target === e.container && (mr = !0);
				};
			};
		}, gr = (e) => {
			e.container.onmousedown = (t) => {
				t.target === e.container && t.preventDefault(), e.popup.onmouseup = function(t) {
					e.popup.onmouseup = () => {}, (t.target === e.popup || t.target instanceof HTMLElement && e.popup.contains(t.target)) && (mr = !0);
				};
			};
		}, _r = (e, t, n) => {
			t.container.onclick = (r) => {
				mr ? mr = !1 : r.target === t.container && _(e.allowOutsideClick) && n(Ft.backdrop);
			};
		}, vr = (e) => typeof e == "object" && !!e && "jquery" in e, yr = (e) => e instanceof Element || vr(e), br = (e) => {
			let t = {};
			return typeof e[0] == "object" && !yr(e[0]) ? Object.assign(t, e[0]) : [
				"title",
				"html",
				"icon"
			].forEach((n, r) => {
				let i = e[r];
				typeof i == "string" || yr(i) ? t[n] = i : i !== void 0 && m(`Unexpected type of ${n}! Expected "string" or "Element", got ${typeof i}`);
			}), t;
		};
		function xr(...e) {
			return new this(...e);
		}
		function Sr(e) {
			class t extends this {
				_main(t, n) {
					return super._main(t, Object.assign({}, e, n));
				}
			}
			return t;
		}
		let Cr = () => a.timeout && a.timeout.getTimerLeft(), wr = () => {
			if (a.timeout) return Te(), a.timeout.stop();
		}, Tr = () => {
			if (a.timeout) {
				let e = a.timeout.start();
				return we(e), e;
			}
		}, Er = () => {
			let e = a.timeout;
			return e && (e.running ? wr() : Tr());
		}, Dr = (e) => {
			if (a.timeout) {
				let t = a.timeout.increase(e);
				return we(t, !0), t;
			}
		}, Or = () => !!(a.timeout && a.timeout.isRunning()), kr = !1, Ar = {};
		function jr(e = "data-swal-template") {
			Ar[e] = this, kr ||= (document.body.addEventListener("click", Mr), !0);
		}
		let Mr = (e) => {
			for (let t = e.target; t && t !== document; t = t.parentNode) for (let e in Ar) {
				let n = t.getAttribute && t.getAttribute(e);
				if (n) {
					Ar[e].fire({ template: n });
					return;
				}
			}
		};
		class Nr {
			constructor() {
				this.events = {};
			}
			_getHandlersByEventName(e) {
				return this.events[e] === void 0 && (this.events[e] = []), this.events[e];
			}
			on(e, t) {
				let n = this._getHandlersByEventName(e);
				n.includes(t) || n.push(t);
			}
			once(e, t) {
				let n = (...r) => {
					this.removeListener(e, n), t.apply(this, r);
				};
				this.on(e, n);
			}
			emit(e, ...t) {
				this._getHandlersByEventName(e).forEach((e) => {
					try {
						e.apply(this, t);
					} catch (e) {
						console.error(e);
					}
				});
			}
			removeListener(e, t) {
				let n = this._getHandlersByEventName(e), r = n.indexOf(t);
				r > -1 && n.splice(r, 1);
			}
			removeAllListeners(e) {
				this.events[e] !== void 0 && (this.events[e].length = 0);
			}
			reset() {
				this.events = {};
			}
		}
		a.eventEmitter = new Nr();
		var Pr = /*#__PURE__*/ Object.freeze({
			__proto__: null,
			argsToParams: br,
			bindClickHandler: jr,
			clickCancel: Pt,
			clickConfirm: Mt,
			clickDeny: Nt,
			enableLoading: Y,
			fire: xr,
			getActions: A,
			getCancelButton: E,
			getCloseButton: ue,
			getConfirmButton: T,
			getContainer: y,
			getDenyButton: D,
			getFocusableElements: de,
			getFooter: le,
			getHtmlContainer: oe,
			getIcon: C,
			getIconContent: ie,
			getImage: se,
			getInputLabel: O,
			getLoader: k,
			getPopup: S,
			getProgressSteps: w,
			getTimerLeft: Cr,
			getTimerProgressBar: j,
			getTitle: ae,
			getValidationMessage: ce,
			increaseTimer: Dr,
			isDeprecatedParameter: $n,
			isLoading: M,
			isTimerRunning: Or,
			isUpdatableParameter: Qn,
			isValidParameter: Zn,
			isVisible: jt,
			mixin: Sr,
			off: (e, t) => {
				a.eventEmitter && (e ? t ? a.eventEmitter.removeListener(e, t) : a.eventEmitter.removeAllListeners(e) : a.eventEmitter.reset());
			},
			on: (e, t) => {
				a.eventEmitter && a.eventEmitter.on(e, t);
			},
			once: (e, t) => {
				a.eventEmitter && a.eventEmitter.once(e, t);
			},
			resumeTimer: Tr,
			showLoading: Y,
			stopTimer: wr,
			toggleTimer: Er
		});
		class Fr {
			constructor(e, t) {
				this.callback = e, this.remaining = t, this.running = !1, this.start();
			}
			start() {
				return this.running || (this.running = !0, this.started = /* @__PURE__ */ new Date(), this.id = setTimeout(this.callback, this.remaining)), this.remaining;
			}
			stop() {
				return this.started && this.running && (this.running = !1, clearTimeout(this.id), this.remaining -= (/* @__PURE__ */ new Date()).getTime() - this.started.getTime()), this.remaining;
			}
			increase(e) {
				let t = this.running;
				return t && this.stop(), this.remaining += e, t && this.start(), this.remaining;
			}
			getTimerLeft() {
				return this.running && (this.stop(), this.start()), this.remaining;
			}
			isRunning() {
				return this.running;
			}
		}
		let Ir = [
			"swal-title",
			"swal-html",
			"swal-footer"
		], Lr = (e) => {
			let t = typeof e.template == "string" ? document.querySelector(e.template) : e.template;
			if (!t) return {};
			let n = t.content;
			return Gr(n), Object.assign(Rr(n), zr(n), Br(n), Vr(n), Hr(n), Ur(n), Wr(n, Ir));
		}, Rr = (e) => {
			let t = {};
			return Array.from(e.querySelectorAll("swal-param")).forEach((e) => {
				Z(e, ["name", "value"]);
				let n = e.getAttribute("name"), r = e.getAttribute("value");
				n && r && (n in X && typeof X[n] == "boolean" ? t[n] = r !== "false" : n in X && typeof X[n] == "object" ? t[n] = JSON.parse(r) : t[n] = r);
			}), t;
		}, zr = (e) => {
			let t = {};
			return Array.from(e.querySelectorAll("swal-function-param")).forEach((e) => {
				let n = e.getAttribute("name"), r = e.getAttribute("value");
				n && r && (t[n] = Function(`return ${r}`)());
			}), t;
		}, Br = (e) => {
			let t = {};
			return Array.from(e.querySelectorAll("swal-button")).forEach((e) => {
				Z(e, [
					"type",
					"color",
					"aria-label"
				]);
				let n = e.getAttribute("type");
				if (!n || ![
					"confirm",
					"cancel",
					"deny"
				].includes(n)) return;
				t[`${n}ButtonText`] = e.innerHTML, t[`show${f(n)}Button`] = !0;
				let r = e.getAttribute("color");
				r !== null && (t[`${n}ButtonColor`] = r);
				let i = e.getAttribute("aria-label");
				i !== null && (t[`${n}ButtonAriaLabel`] = i);
			}), t;
		}, Vr = (e) => {
			let t = {}, n = e.querySelector("swal-image");
			if (n) {
				Z(n, [
					"src",
					"width",
					"height",
					"alt"
				]);
				let e = n.getAttribute("src");
				e !== null && (t.imageUrl = e || void 0);
				let r = n.getAttribute("width");
				r !== null && (t.imageWidth = r || void 0);
				let i = n.getAttribute("height");
				i !== null && (t.imageHeight = i || void 0);
				let a = n.getAttribute("alt");
				a !== null && (t.imageAlt = a || void 0);
			}
			return t;
		}, Hr = (e) => {
			let t = {}, n = e.querySelector("swal-icon");
			return n && (Z(n, ["type", "color"]), n.hasAttribute("type") && (t.icon = n.getAttribute("type")), n.hasAttribute("color") && (t.iconColor = n.getAttribute("color")), t.iconHtml = n.innerHTML), t;
		}, Ur = (e) => {
			let t = {}, n = e.querySelector("swal-input");
			n && (Z(n, [
				"type",
				"label",
				"placeholder",
				"value"
			]), t.input = n.getAttribute("type") || "text", n.hasAttribute("label") && (t.inputLabel = n.getAttribute("label")), n.hasAttribute("placeholder") && (t.inputPlaceholder = n.getAttribute("placeholder")), n.hasAttribute("value") && (t.inputValue = n.getAttribute("value")));
			let r = Array.from(e.querySelectorAll("swal-input-option"));
			return r.length && (t.inputOptions = {}, r.forEach((e) => {
				Z(e, ["value"]);
				let n = e.getAttribute("value");
				if (!n) return;
				let r = e.innerHTML;
				t.inputOptions[n] = r;
			})), t;
		}, Wr = (e, t) => {
			let n = {};
			for (let r in t) {
				let i = t[r], a = e.querySelector(i);
				a && (Z(a, []), n[i.replace(/^swal-/, "")] = a.innerHTML.trim());
			}
			return n;
		}, Gr = (e) => {
			let t = Ir.concat([
				"swal-param",
				"swal-function-param",
				"swal-button",
				"swal-image",
				"swal-icon",
				"swal-input",
				"swal-input-option"
			]);
			Array.from(e.children).forEach((e) => {
				let n = e.tagName.toLowerCase();
				t.includes(n) || p(`Unrecognized element <${n}>`);
			});
		}, Z = (e, t) => {
			Array.from(e.attributes).forEach((n) => {
				t.indexOf(n.name) === -1 && p([`Unrecognized attribute "${n.name}" on <${e.tagName.toLowerCase()}>.`, `${t.length ? `Allowed attributes are: ${t.join(", ")}` : "To set the value, use HTML within the element."}`]);
			});
		}, Kr = (e) => {
			var t, n;
			let r = y(), i = S();
			if (!r || !i) return;
			typeof e.willOpen == "function" && e.willOpen(i), (t = a.eventEmitter) == null || t.emit("willOpen", i);
			let o = window.getComputedStyle(document.body).overflowY;
			if (Xr(r, i, e), setTimeout(() => {
				Jr(r, i);
			}, 10), fe() && (Yr(r, e.scrollbarPadding !== void 0 && e.scrollbarPadding, o), Kt()), Yt && e.backdrop === !1 && i.scrollHeight > r.clientHeight && (r.style.pointerEvents = "auto"), !pe() && !a.previousActiveElement && (a.previousActiveElement = document.activeElement), typeof e.didOpen == "function") {
				let t = e.didOpen;
				setTimeout(() => t(i));
			}
			(n = a.eventEmitter) == null || n.emit("didOpen", i);
		}, qr = (e) => {
			let t = S();
			if (!t || e.target !== t) return;
			let n = y();
			n && (t.removeEventListener("animationend", qr), t.removeEventListener("transitionend", qr), n.style.overflowY = "auto", R(n, l["no-transition"]));
		}, Jr = (e, t) => {
			Ce(t) ? (e.style.overflowY = "hidden", t.addEventListener("animationend", qr), t.addEventListener("transitionend", qr)) : e.style.overflowY = "auto";
		}, Yr = (e, t, n) => {
			Xt(), t && n !== "hidden" && an(n), setTimeout(() => {
				e.scrollTop = 0;
			});
		}, Xr = (e, t, n) => {
			var r;
			(r = n.showClass) != null && r.backdrop && L(e, n.showClass.backdrop), n.animation ? (t.style.setProperty("opacity", "0", "important"), V(t, "grid"), setTimeout(() => {
				var e;
				(e = n.showClass) != null && e.popup && L(t, n.showClass.popup), t.style.removeProperty("opacity");
			}, 10)) : V(t, "grid"), L([document.documentElement, document.body], l.shown), n.heightAuto && n.backdrop && !n.toast && L([document.documentElement, document.body], l["height-auto"]);
		};
		var Zr = {
			email: (e, t) => /^[a-zA-Z0-9.+_'-]+@[a-zA-Z0-9.-]+\.[a-zA-Z0-9-]+$/.test(e) ? Promise.resolve() : Promise.resolve(t || "Invalid email address"),
			url: (e, t) => /^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._+~#=]{1,256}\.[a-z]{2,63}\b([-a-zA-Z0-9@:%_+.~#?&/=]*)$/.test(e) ? Promise.resolve() : Promise.resolve(t || "Invalid URL")
		};
		function Qr(e) {
			e.inputValidator || (e.input === "email" && (e.inputValidator = Zr.email), e.input === "url" && (e.inputValidator = Zr.url));
		}
		function $r(e) {
			(!e.target || typeof e.target == "string" && !document.querySelector(e.target) || typeof e.target != "string" && !e.target.appendChild) && (p("Target parameter is not valid, defaulting to \"body\""), e.target = "body");
		}
		function ei(e) {
			Qr(e), e.showLoaderOnConfirm && !e.preConfirm && p("showLoaderOnConfirm is set to true, but preConfirm is not defined.\nshowLoaderOnConfirm should be used together with preConfirm, see usage example:\nhttps://sweetalert2.github.io/#ajax-request"), $r(e), typeof e.title == "string" && (e.title = e.title.split("\n").join("<br />")), Ne(e);
		}
		let Q;
		var ti = /*#__PURE__*/ new WeakMap();
		class $ {
			constructor(...e) {
				if (r(this, ti, Promise.resolve({
					isConfirmed: !1,
					isDenied: !1,
					isDismissed: !0
				})), typeof window > "u") return;
				Q = this;
				let t = Object.freeze(this.constructor.argsToParams(e));
				this.params = t, this.isAwaitingPromise = !1, i(ti, this, this._main(Q.params));
			}
			_main(e, t = {}) {
				if (rr(Object.assign({}, t, e)), a.currentInstance) {
					let e = q.swalPromiseResolve.get(a.currentInstance), { isAwaitingPromise: t } = a.currentInstance;
					a.currentInstance._destroy(), t || e({ isDismissed: !0 }), fe() && qt();
				}
				a.currentInstance = Q;
				let n = ri(e, t);
				ei(n), Object.freeze(n), a.timeout && (a.timeout.stop(), delete a.timeout), clearTimeout(a.restoreFocusTimeout);
				let r = ii(Q);
				return At(Q, n), G.innerParams.set(Q, n), ni(Q, r, n);
			}
			then(e) {
				return n(ti, this).then(e);
			}
			finally(e) {
				return n(ti, this).finally(e);
			}
		}
		let ni = (e, t, n) => new Promise((r, i) => {
			let o = (t) => {
				e.close({
					isDismissed: !0,
					dismiss: t,
					isConfirmed: !1,
					isDenied: !1
				});
			};
			q.swalPromiseResolve.set(e, r), q.swalPromiseReject.set(e, i), t.confirmButton.onclick = () => {
				On(e);
			}, t.denyButton.onclick = () => {
				kn(e);
			}, t.cancelButton.onclick = () => {
				An(e, o);
			}, t.closeButton.onclick = () => {
				o(Ft.close);
			}, dr(n, t, o), Lt(a, n, o), _n(e, n), Kr(n), ai(a, n, o), oi(t, n), setTimeout(() => {
				t.container.scrollTop = 0;
			});
		}), ri = (e, t) => {
			let n = Lr(e), r = Object.assign({}, X, t, n, e);
			return r.showClass = Object.assign({}, X.showClass, r.showClass), r.hideClass = Object.assign({}, X.hideClass, r.hideClass), r.animation === !1 && (r.showClass = { backdrop: "swal2-noanimation" }, r.hideClass = {}), r;
		}, ii = (e) => {
			let t = {
				popup: S(),
				container: y(),
				actions: A(),
				confirmButton: T(),
				denyButton: D(),
				cancelButton: E(),
				loader: k(),
				closeButton: ue(),
				validationMessage: ce(),
				progressSteps: w()
			};
			return G.domCache.set(e, t), t;
		}, ai = (e, t, n) => {
			let r = j();
			H(r), t.timer && (e.timeout = new Fr(() => {
				n("timer"), delete e.timeout;
			}, t.timer), t.timerProgressBar && r && (V(r), F(r, t, "timerProgressBar"), setTimeout(() => {
				e.timeout && e.timeout.running && we(t.timer);
			})));
		}, oi = (e, t) => {
			t.toast || (_(t.allowEnterKey) ? si(e) || ci(e, t) || Rt(-1, 1) : (g("allowEnterKey", "preConfirm: () => false"), e.popup.focus()));
		}, si = (e) => {
			let t = Array.from(e.popup.querySelectorAll("[autofocus]"));
			for (let e of t) if (e instanceof HTMLElement && U(e)) return e.focus(), !0;
			return !1;
		}, ci = (e, t) => t.focusDeny && U(e.denyButton) ? (e.denyButton.focus(), !0) : t.focusCancel && U(e.cancelButton) ? (e.cancelButton.focus(), !0) : t.focusConfirm && U(e.confirmButton) ? (e.confirmButton.focus(), !0) : !1;
		$.prototype.disableButtons = Un, $.prototype.enableButtons = Hn, $.prototype.getInput = zn, $.prototype.disableInput = Gn, $.prototype.enableInput = Wn, $.prototype.hideLoading = Ln, $.prototype.disableLoading = Ln, $.prototype.showValidationMessage = Kn, $.prototype.resetValidationMessage = qn, $.prototype.close = J, $.prototype.closePopup = J, $.prototype.closeModal = J, $.prototype.closeToast = J, $.prototype.rejectPromise = un, $.prototype.update = ir, $.prototype._destroy = or, Object.assign($, Pr), Object.keys(ur).forEach((e) => {
			$[e] = function(...t) {
				if (Q && Q[e]) return Q[e](...t);
			};
		}), $.DismissReason = Ft, $.version = "11.26.25";
		let li = $;
		return li.default = li, li;
	})), n !== void 0 && n.Sweetalert2 && (n.swal = n.sweetAlert = n.Swal = n.SweetAlert = n.Sweetalert2);
})))()), d = (e) => window.donationTranslations?.[e] ?? e, f = window.donationRoutes, p = (e) => String(e ?? "").replace(/[&<>"']/g, (e) => ({
	"&": "&amp;",
	"<": "&lt;",
	">": "&gt;",
	"\"": "&quot;",
	"'": "&#39;"
})[e]);
async function m(e, t = {}) {
	let n = await fetch(e, {
		credentials: "same-origin",
		...t,
		headers: {
			Accept: "application/json",
			...t.body instanceof FormData ? {} : { "Content-Type": "application/json" },
			"X-CSRF-TOKEN": document.querySelector("meta[name=\"csrf-token\"]").content,
			...t.headers
		}
	});
	if (n.status === 204) return null;
	let r = await n.json();
	if (!n.ok) throw n.status === 401 ? Error(d("Please log in as administrator.")) : Error(r.errors ? Object.values(r.errors).flat().join("\n") : r.message || d("The request failed."));
	return r;
}
var h = (e, t, n = "POST") => m(e, {
	method: n,
	body: JSON.stringify(t)
}), ee = (e) => u.default.fire({
	confirmButtonColor: "#e91335",
	cancelButtonColor: "#64748b",
	confirmButtonText: d("OK"),
	heightAuto: !1,
	returnFocus: !1,
	width: "340px",
	padding: "22px",
	showClass: {
		popup: "donation-alert-enter",
		backdrop: "donation-alert-backdrop-enter",
		icon: ""
	},
	hideClass: {
		popup: "donation-alert-leave",
		backdrop: "donation-alert-backdrop-leave",
		icon: ""
	},
	buttonsStyling: !1,
	customClass: {
		container: "donation-alert-container",
		popup: "donation-alert",
		confirmButton: "donation-alert-confirm",
		cancelButton: "donation-alert-cancel"
	},
	...e
}), g = (e) => ee({
	icon: "success",
	titleText: d("Success"),
	text: e
}), _ = (e) => ee({
	icon: "error",
	titleText: d("Action failed"),
	text: e
}), te = (e) => ee({
	icon: "info",
	titleText: d("Notice"),
	text: e
});
async function v(e) {
	return (await ee({
		icon: "warning",
		titleText: d("Are you sure?"),
		text: e,
		showCancelButton: !0,
		confirmButtonText: d("Confirm"),
		cancelButtonText: d("Cancel"),
		focusCancel: !0
	})).isConfirmed;
}
// resources/js/donation-locations.js
var ne = () => [
	"district_id",
	"upazila_id",
	"union_id"
].map((e, t) => `<div class="field"><label>${[
	d("District"),
	d("Upazila"),
	d("Union")
][t]}</label><select name="${e}" data-location="${e}"><option value="">${d("Select (optional)")}</option></select></div>`).join("");
async function re(e, t = {}) {
	let n = e.querySelector("[data-location=\"district_id\"]"), r = e.querySelector("[data-location=\"upazila_id\"]"), i = e.querySelector("[data-location=\"union_id\"]");
	if (!n) return;
	let a = (e, t, n) => {
		e.innerHTML = d("<option value=\"\">Select (optional)</option>") + t.map((e) => `<option value="${e.id}" ${Number(n) === e.id ? "selected" : ""}>${p(document.documentElement.lang === "bn" && e.name_bn || e.name)}</option>`).join("");
	};
	a(r, []), a(i, []), a(n, (await m(f.locations)).data, t.district_id), t.district_id && a(r, (await m(`${f.locations}?district_id=${t.district_id}`)).data, t.upazila_id), t.upazila_id && a(i, (await m(`${f.locations}?upazila_id=${t.upazila_id}`)).data, t.union_id), n.onchange = async () => {
		if (n.setCustomValidity(""), a(r, []), a(i, []), n.value) try {
			let e = n.value, t = await m(`${f.locations}?district_id=${e}`);
			n.value === e && a(r, t.data);
		} catch (e) {
			n.setCustomValidity(e.message), n.reportValidity();
		}
	}, r.onchange = async () => {
		if (r.setCustomValidity(""), a(i, []), r.value) try {
			let e = r.value, t = await m(`${f.locations}?upazila_id=${e}`);
			r.value === e && a(i, t.data);
		} catch (e) {
			r.setCustomValidity(e.message), r.reportValidity();
		}
	};
}
// resources/js/donation-dashboard.js
var y = (e) => document.getElementById(e), b = [], x = [], S = null, C = 1, ie, ae, oe, se = y("donorForm").innerHTML, w = document.body.dataset.adminPage || "dashboard";
function ce(e) {
	return te(e);
}
var T;
function E() {
	y("modalOverlay").classList.remove("show"), document.body.style.overflow = "", T?.focus();
}
function D(e, t, n, r = {}) {
	oe = {
		[d("New donor")]: d("Donor created successfully."),
		[d("Edit donor")]: d("Donor updated successfully."),
		[d("Blood requests")]: d("Blood request created successfully."),
		[d("Record donation")]: d("Donation recorded successfully."),
		[d("Assign donor")]: d("Donor assigned successfully."),
		[d("Donor response")]: d("Donor response updated successfully.")
	}[e] || d("Saved successfully."), y("modalOverlay").querySelector(".modal").classList.toggle("request-details-modal", !!r.requestDetails), y("modalOverlay").querySelector("h2").textContent = e, y("modalOverlay").querySelector(".modal-header p").textContent = r.description || d("Add details with consent. History updates from verified donations."), y("donorForm").innerHTML = t + d("<p id=\"formError\" role=\"alert\" style=\"color:#c92d3b;white-space:pre-line\"></p><div class=\"modal-actions\"><button type=\"button\" class=\"btn\" onclick=\"closeModal()\">Cancel</button><button type=\"submit\" class=\"btn primary\">Save</button></div>"), ie = n, y("modalOverlay").classList.contains("show") || (T = document.activeElement), y("modalOverlay").classList.add("show"), document.body.style.overflow = "hidden", y("modalOverlay").querySelector(".close").focus();
}
var O = (e, t, n = "text", r = "", i = !0) => `<div class="field"><label>${p(e)}</label><input name="${t}" type="${n}" value="${p(r)}" ${i ? "required" : ""}></div>`, k = (e) => `<div class="field"><label>${d("Blood group")}</label><select name="blood_group_id" required>${b.map((t) => `<option value="${t.id}" ${Number(e) === t.id ? "selected" : ""}>${p(t.name)}</option>`).join("")}</select></div>`;
async function A() {
	S = (await m(f.admin)).data;
	let e = [
		[
			d("Total active donors"),
			S.donors,
			"users",
			"r"
		],
		[
			d("Available donors"),
			S.available_donors,
			"check-circle",
			"g"
		],
		[
			d("Pending requests"),
			S.pending_requests,
			"clock",
			"o"
		],
		[
			d("Urgent requests"),
			S.urgent_requests,
			"triangle-warning",
			"r"
		],
		[
			d("Completed donations"),
			S.donations,
			"heart",
			"r"
		]
	];
	y("stats") && (y("stats").innerHTML = e.map(([e, t, n, r]) => `<div class="stat"><div class="stat-icon ${r}"><i class="fi fi-rr-${n}" aria-hidden="true"></i></div><div><div class="stat-label">${e}</div><div class="stat-value">${t}</div></div></div>`).join("")), y("openRequestCount").textContent = S.open_requests;
	let t = Math.max(...S.blood_groups.map((e) => e.donors_count), 1);
	y("bloodBars") && (y("bloodBars").innerHTML = S.blood_groups.map((e) => `<div class="bloodrow"><strong>${p(e.name)}</strong><div class="progress"><div style="width:${e.donors_count / t * 100}%"></div></div><small>${e.donors_count}</small></div>`).join("")), le();
}
function le() {
	if (!S || !y("chart")) return;
	let e = S.monthly_donations.slice(-Number(y("chartPeriod").value));
	y("chart").innerHTML = "<div style=\"display:flex;align-items:end;gap:6px;height:190px\">" + e.map((t) => `<div style="flex:1;text-align:center"><b class="chart-value">${t.donations}</b><div style="height:${t.donations / Math.max(...e.map((e) => e.donations), 1) * 140}px;min-height:2px;background:#ed1638;border-radius:5px;position:relative"></div><small class="chart-month">${p(t.month)}</small></div>`).join("") + "</div>";
}
async function j(e = 1) {
	if (!y("donorRows")) return;
	let t = new URLSearchParams({
		page: e,
		q: y("search").value,
		area: y("areaFilter").value,
		availability: y("statusFilter").value
	}), n = b.find((e) => e.name === y("groupFilter").value);
	n && t.set("blood_group_id", n.id);
	let r = await m(`${f.adminDonors}?${t}`);
	x = r.data, C = e, y("donorRows").innerHTML = x.map((e, t) => `<tr><td>${r.meta.from + t}.</td><td><div class="donorcell"><span class="donoravatar" aria-hidden="true">${p(e.name.trim().split(/\s+/u).slice(0, 2).map((e) => Array.from(e)[0]).join("").toLocaleUpperCase())}</span><div><strong>${p(e.name)}</strong><small class="donor-state ${e.status ? "active" : ""}">${e.status ? d("Active") : d("Awaiting verification")}</small></div></div></td><td><span class="bloodpill">${p(e.bloodGroup?.name)}</span></td><td>${p(e.area)}<small>${p(e.address)}</small></td><td><span class="table-date">${p(e.last_donation_date?.slice(0, 10) || d("No information"))}</span></td><td><span class="status ${e.is_available ? "" : "busy"}">${e.is_available ? d("Available") : d("Unavailable")}</span></td><td><a class="phone-link" href="tel:${p(e.phone || "")}">${p(e.phone || d("No consent"))}</a><small>${e.total_donations} ${d("verified donations")}</small></td><td class="donor-actions"><details class="row-menu"><summary aria-label="${d("Actions")}"><i class="fi fi-rr-angle-small-down menu-chevron" aria-hidden="true"></i></summary><div><button class="row-btn" data-action="donor-history" data-id="${e.id}">${d("History")}</button><button class="row-btn" data-action="edit-donor" data-id="${e.id}">${d("Edit")}</button><button class="row-btn" data-action="delete-donor" data-id="${e.id}">${d("Delete")}</button><button class="row-btn menu-toggle" role="switch" aria-checked="${!!e.is_available}" data-action="toggle-donor" data-id="${e.id}"><span>${d("Available")}</span><span class="menu-switch" aria-hidden="true"></span></button><button class="row-btn menu-toggle" role="switch" aria-checked="${!!e.status}" data-action="approve-donor" data-id="${e.id}"><span>${d("Active")}</span><span class="menu-switch" aria-hidden="true"></span></button></div></details></td></tr>`).join("") || d("<tr><td colspan=\"8\">No donors.</td></tr>"), y("tableCount").textContent = `${r.meta.from || 0}–${r.meta.to || 0} / ${r.meta.total}`, y("pagination").innerHTML = `<button data-donor-page="${e - 1}" ${e <= 1 ? "disabled" : ""}>‹</button><button class="current" aria-current="page">${e}</button><button data-donor-page="${e + 1}" ${e >= r.meta.last_page ? "disabled" : ""}>›</button>`;
	let i = [...new Set(x.map((e) => e.area).filter(Boolean))], a = y("areaFilter").value;
	y("areaFilter").innerHTML = d("<option value=\"\">All areas</option>") + [...new Set([a, ...i].filter(Boolean))].map((e) => `<option ${e === a ? "selected" : ""}>${p(e)}</option>`).join("");
}
function ue() {
	clearTimeout(ae), ae = setTimeout(() => j().catch((e) => _(e.message)), 250);
}
function de() {
	D(d("New donor"), se + ne(), async (e) => {
		let t = b.find((t) => t.name === e.group);
		if (!t) throw Error(d("Select a blood group."));
		await h(f.adminDonors, {
			name: e.name,
			phone: e.phone,
			blood_group_id: t.id,
			area: e.area,
			address: e.address,
			is_available: !0,
			consent_to_contact: !0,
			district_id: e.district_id || null,
			upazila_id: e.upazila_id || null,
			union_id: e.union_id || null
		}), await j(), await A();
	}), re(y("donorForm")).catch((e) => _(e.message));
	let e = y("donorForm").querySelectorAll(".modal-actions");
	e.length > 1 && e[0].remove();
}
async function fe(e) {
	let t = (await m(`${f.adminDonors}/${e}`)).data;
	D(d("Edit donor"), O(d("Name"), "name", "text", t.name) + O(d("Phone (enter to change number)"), "phone", "tel", t.phone || "", !1) + k(t.blood_group_id) + O(d("Area"), "area", "text", t.area, !1) + O(d("Address"), "address", "text", t.address, !1) + ne() + `<label><input type="checkbox" name="consent_to_contact" ${t.consent_to_contact ? "checked" : ""}> ${d("Consent to contact given")}</label>`, async (t) => {
		t.phone || delete t.phone, await h(`${f.adminDonors}/${e}`, {
			...t,
			consent_to_contact: t.consent_to_contact === "on"
		}, "PATCH"), await j(C), await A();
	}), await re(y("donorForm"), t);
}
function pe() {
	D(d("Blood requests"), k() + ne() + O(d("Patient name"), "patient_name") + O(d("Contact name"), "contact_name") + O(d("Contact phone"), "contact_phone", "tel") + O(d("Hospital"), "hospital_name") + O(d("Hospital address"), "hospital_address", "text", "", !1) + O(d("Number of bags"), "bags_needed", "number", 1) + O(d("Needed at"), "needed_at", "datetime-local") + O(d("Expires at (optional)"), "expires_at", "datetime-local", "", !1) + d("<div class=\"field\"><label>Urgency</label><select name=\"urgency\"><option value=\"normal\">Normal</option><option value=\"urgent\">Urgent</option><option value=\"emergency\">Very urgent</option><option value=\"critical\">Critical</option></select></div>"), async (e) => {
		await h(f.requests, {
			...e,
			needed_at: new Date(e.needed_at).toISOString(),
			expires_at: e.expires_at ? new Date(e.expires_at).toISOString() : null
		}), await M(), await A();
	}), re(y("donorForm")).catch((e) => _(e.message));
}
async function M(e = 1) {
	if (!y("requestList")) return;
	let t = await m(`${f.requests}?page=${e}`);
	y("requestList").innerHTML = t.data.map((e, n) => {
		let r = e.patient_name.trim().split(/\s+/u).slice(0, 2).map((e) => Array.from(e)[0]).join("").toLocaleUpperCase(), i = `<button class="row-btn" data-action="request-details" data-id="${e.id}">${d("Details")}</button>` + (e.request_status === "pending" ? `<button class="row-btn" data-action="verify-request" data-id="${e.id}">${d("Verify")}</button><button class="row-btn" data-action="cancel-request" data-id="${e.id}">${d("Cancel")}</button>` : ["verified", "in_progress"].includes(e.request_status) ? `<button class="row-btn" data-action="assign-donor" data-id="${e.id}">${d("Assign donor")}</button><button class="row-btn" data-action="match" data-id="${e.id}">${d("Match donors")}</button><button class="row-btn" data-action="cancel-request" data-id="${e.id}">${d("Cancel")}</button>` : "");
		return `<tr><td>${t.meta.from + n}.</td><td><div class="donorcell"><span class="donoravatar" aria-hidden="true">${p(r)}</span><strong>${p(e.patient_name)}</strong></div></td><td><span class="bloodpill">${p(e.bloodGroup?.name)}</span></td><td>${p(e.hospital_name)}<small>${p(e.hospital_address || "")}</small></td><td>${e.bags_needed}</td><td><span class="status ${[
			"verified",
			"in_progress",
			"completed"
		].includes(e.request_status) ? "" : "busy"}">${p(e.request_status.replaceAll("_", " "))}</span></td><td><span class="bloodpill request-urgency ${e.urgency === "normal" ? "normal" : ""}">${p(e.urgency)}</span></td><td><a class="phone-link" href="tel:${p(e.contact_phone)}">${p(e.contact_phone)}</a><small>${p(e.contact_name)}</small></td><td class="donor-actions"><details class="row-menu"><summary aria-label="${d("Actions")}"><i class="fi fi-rr-angle-small-down menu-chevron" aria-hidden="true"></i></summary><div>${i}</div></details></td></tr>`;
	}).join("") || `<tr><td colspan="9" class="empty">${d("No requests.")}</td></tr>`, y("requestTableCount").textContent = `${t.meta.from || 0}–${t.meta.to || 0} / ${t.meta.total}`, y("requestPagination").innerHTML = `<button data-action="requests-page" data-id="${e - 1}" aria-label="${d("Previous page")}" ${e <= 1 ? "disabled" : ""}>‹</button><button class="current" aria-current="page">${e}</button><button data-action="requests-page" data-id="${e + 1}" aria-label="${d("Next page")}" ${e >= t.meta.last_page ? "disabled" : ""}>›</button>`;
}
var N = (e, t) => `<button data-action="${t}" data-id="${e.current_page - 1}" aria-label="${d("Previous page")}" ${e.current_page <= 1 ? "disabled" : ""}>‹</button><button class="current" aria-current="page">${e.current_page}</button><button data-action="${t}" data-id="${e.current_page + 1}" aria-label="${d("Next page")}" ${e.current_page >= e.last_page ? "disabled" : ""}>›</button>`;
async function P(e) {
	let t = (await m(`${f.requests}/${e}`)).data, n = new Set(t.request_donors.map((e) => Number(e.blood_donor_id)));
	D(d("Assign donor"), `<div class="assign-context"><span class="bloodpill">${p(t.bloodGroup?.name || "")}</span><div><strong>${p(t.patient_name)}</strong><small>${p(t.hospital_name)}</small></div></div><label class="request-search"><i class="fi fi-rr-search" aria-hidden="true"></i><input type="search" id="assignDonorSearch" maxlength="100" placeholder="${p(d("Search donor by name"))}" aria-label="${p(d("Search donor by name"))}" aria-controls="assignDonorResults"></label><h3 class="assign-heading">${d("Matched donors")}</h3><p id="assignDonorStatus" class="assign-status" role="status"></p><div id="assignDonorResults" class="assign-results"></div><div id="assignDonorPages" class="pagination"></div>`, async (t) => {
		if (!t.blood_donor_id) throw Error(d("Select a donor to assign."));
		await h(`${f.requests}/${e}/assign-donor`, { blood_donor_id: t.blood_donor_id }), await M(), await A();
	}, { description: d("Search by name, select a matching donor, then update to assign.") });
	let r = y("donorForm").querySelector("[type=submit]");
	r.textContent = d("Update"), r.disabled = !0;
	let i = y("assignDonorSearch"), a = y("assignDonorResults"), o = y("assignDonorStatus"), s = y("assignDonorPages"), c, l = 0;
	async function u(e = 1) {
		let c = ++l;
		r.disabled = !0, a.replaceChildren(), s.replaceChildren(), o.textContent = d("Searching donors…");
		let u = new URLSearchParams({
			q: i.value.trim(),
			page: e
		});
		for (let e of [
			"blood_group_id",
			"district_id",
			"upazila_id",
			"union_id"
		]) t[e] && u.set(e, t[e]);
		try {
			let t = await m(`${f.donors}?${u}`);
			if (c !== l || !a.isConnected) return;
			o.textContent = t.data.length ? `${t.meta.total} ${d("donors matched")}` : d("No donors match your search."), a.innerHTML = t.data.map((e) => {
				let t = n.has(Number(e.id));
				return `<label class="assign-option ${t ? "is-assigned" : ""}"><input type="radio" name="blood_donor_id" value="${e.id}" required ${t ? "disabled" : ""}><span class="assign-donor-info"><strong>${p(e.name)}</strong><small>${p([
					e.union,
					e.upazila,
					e.district
				].filter(Boolean).join(", ") || d("No information"))}</small></span><span class="${t ? "request-response" : "bloodpill"}">${p(t ? d("Assigned") : e.blood_group)}</span></label>`;
			}).join(""), t.meta.last_page > 1 && (s.innerHTML = `<button type="button" data-assign-page="${e - 1}" ${e <= 1 ? "disabled" : ""} aria-label="${p(d("Previous"))}">‹</button><span>${e} / ${t.meta.last_page}</span><button type="button" data-assign-page="${e + 1}" ${e >= t.meta.last_page ? "disabled" : ""} aria-label="${p(d("Next"))}">›</button>`);
		} catch (e) {
			c === l && a.isConnected && (o.textContent = e.message);
		}
	}
	i.addEventListener("input", () => {
		++l, clearTimeout(c), r.disabled = !0, a.replaceChildren(), s.replaceChildren(), o.textContent = d("Searching donors…"), c = setTimeout(() => u(), 250);
	}), a.addEventListener("change", () => {
		r.disabled = !a.querySelector("input:checked");
	}), s.addEventListener("click", (e) => {
		let t = e.target.closest("[data-assign-page]");
		t && u(Number(t.dataset.assignPage));
	}), i.focus(), await u();
}
async function me(e) {
	let t = (await m(`${f.requests}/${e}`)).data, n = ["verified", "in_progress"].includes(t.request_status), r = (e) => e ? new Intl.DateTimeFormat(document.documentElement.lang === "bn" ? "bn-BD" : "en-GB", {
		timeZone: "Asia/Dhaka",
		day: "numeric",
		month: "short",
		year: "numeric",
		hour: "numeric",
		minute: "2-digit"
	}).format(new Date(e)) : d("No information"), i = (e) => p(String(e || d("New")).replaceAll("_", " ")), a = t.patient_name.trim().split(/\s+/u).slice(0, 2).map((e) => Array.from(e)[0]).join("").toLocaleUpperCase(), o = t.request_donors.length ? t.request_donors.map((t) => {
		let r = t.blood_donor || {}, a = (r.name || "?").trim().split(/\s+/u).slice(0, 2).map((e) => Array.from(e)[0]).join("").toLocaleUpperCase(), o = n && t.response_status !== "donated" ? `<div class="request-donor-actions"><button type="button" class="btn" data-action="respond" data-request="${e}" data-id="${t.id}">${d("Update response")}</button><button type="button" class="btn primary" data-action="record-donation" data-request="${e}" data-id="${t.blood_donor_id}">${d("Record donation")}</button></div>` : "";
		return `<article class="request-donor"><span class="request-avatar" aria-hidden="true">${p(a)}</span><div class="request-donor-info"><strong>${p(r.name || d("No information"))}</strong><a href="tel:${p(r.phone || "")}">${p(r.phone || d("No information"))}</a></div><span class="request-response request-response--${p(t.response_status || "pending")}">${i(t.response_status || "pending")}</span>${o}</article>`;
	}).join("") : `<p class="request-empty">${d("No donors have been matched yet.")}</p>`, s = t.status_logs.length ? t.status_logs.map((e) => `<li><span class="request-timeline-dot" aria-hidden="true"></span><div><strong>${i(e.to_status)}</strong><p>${i(e.from_status || d("New"))} → ${i(e.to_status)}</p><time>${p(r(e.created_at))}</time></div></li>`).join("") : `<p class="request-empty">${d("No status updates yet.")}</p>`, c = t.donations.length ? t.donations.map((e) => `<li><strong>${e.bags} ${d("bags")}</strong><span>${p(r(e.donated_at))}</span></li>`).join("") : `<p class="request-empty">${d("No donations recorded yet.")}</p>`, l = `<section class="request-details" aria-label="${d("Request details")}"><div class="request-overview"><span class="request-avatar request-avatar--patient" aria-hidden="true">${p(a)}</span><div><p class="request-eyebrow">${d("Patient")}</p><h3>${p(t.patient_name)}</h3><p class="request-hospital"><i class="fi fi-rr-hospital" aria-hidden="true"></i>${p(t.hospital_name)}</p></div><div class="request-badges"><span class="status ${[
		"verified",
		"in_progress",
		"completed"
	].includes(t.request_status) ? "" : "busy"}">${i(t.request_status)}</span><span class="request-urgency">${i(t.urgency)}</span></div></div><dl class="request-facts"><div><dt>${d("Blood group")}</dt><dd><span class="bloodpill">${p(t.bloodGroup?.name || d("No information"))}</span></dd></div><div><dt>${d("Bags needed")}</dt><dd>${t.bags_needed} ${d("bags")}</dd></div><div><dt>${d("Needed at")}</dt><dd>${p(r(t.needed_at))}</dd></div><div><dt>${d("Contact")}</dt><dd><strong>${p(t.contact_name)}</strong><a href="tel:${p(t.contact_phone)}">${p(t.contact_phone)}</a></dd></div></dl><section class="request-section"><div class="request-section-heading"><div><h3>${d("Matched donors")}</h3><p>${t.request_donors.length} ${d("donors matched")}</p></div></div><label class="request-search"><i class="fi fi-rr-search" aria-hidden="true"></i><input type="search" id="matchedDonorSearch" placeholder="${p(d("Search matched donors"))}" aria-label="${p(d("Search matched donors"))}" aria-controls="matchedDonorList"></label><div class="request-donors" id="matchedDonorList">${o}</div><p class="request-empty" id="matchedDonorEmpty" role="status" hidden>${d("No donors match your search.")}</p></section><details class="request-history"><summary>${d("Status history")} · ${d("Donation history")}</summary><div class="request-history-grid"><section class="request-section"><h3>${d("Status history")}</h3><ol class="request-timeline">${s}</ol></section><section class="request-section"><h3>${d("Donation history")}</h3><ul class="request-donations">${c}</ul></section></div></details></section>`;
	D(d("Request details"), l, async () => {}, {
		requestDetails: !0,
		description: d("Review request information, matched donors, and progress at a glance.")
	}), y("donorForm").querySelector("[type=submit]").hidden = !0, y("donorForm").querySelector(".modal-actions .btn").textContent = d("Close");
}
function F(e = "", t = "") {
	D(d("Record donation"), O(d("Donor ID"), "blood_donor_id", "number", e) + O(d("Requests ID (Optional)"), "blood_request_id", "number", t, !1) + O(d("Donation date"), "donated_at", "date", new Intl.DateTimeFormat("en-CA", {
		timeZone: "Asia/Dhaka",
		year: "numeric",
		month: "2-digit",
		day: "2-digit"
	}).format(/* @__PURE__ */ new Date())) + O(d("Hospital"), "hospital_name", "text", "", !1), async (e) => {
		await h(f.donations, {
			...e,
			blood_request_id: e.blood_request_id || null,
			bags: 1
		}), await A(), await j(), await M(), w === "donations" && await he();
	});
}
async function I(e = 1) {
	let t = await m(`${f.contacts}?page=${e}`), n = t.data.map((e, n) => {
		let r = e.requester_name || d("No information"), i = r.trim().split(/\s+/u).slice(0, 2).map((e) => Array.from(e)[0]).join("").toLocaleUpperCase(), a = e.contact_status === "pending" ? `<button class="row-btn" data-action="contact-approved" data-id="${e.id}">${d("Approve")}</button><button class="row-btn" data-action="contact-rejected" data-id="${e.id}">${d("Reject")}</button>` : e.contact_status === "approved" ? `<button class="row-btn" data-action="contact-completed" data-id="${e.id}">${d("Completed")}</button><button class="row-btn" data-action="contact-rejected" data-id="${e.id}">${d("Reject")}</button>` : "";
		return `<tr><td>${t.from + n}.</td><td><div class="donorcell"><span class="donoravatar" aria-hidden="true">${p(i)}</span><strong>${p(r)}</strong></div></td><td><a class="phone-link" href="tel:${p(e.requester_phone || "")}">${p(e.requester_phone || d("No information"))}</a></td><td>${p(e.blood_donor?.name || d("No information"))}<small>${p(e.blood_donor?.phone || "")}</small></td><td>${p(e.message || d("No information"))}</td><td><span class="status ${["approved", "completed"].includes(e.contact_status) ? "" : "busy"}">${p(d({
			pending: "Pending",
			approved: "Approved",
			rejected: "Rejected",
			completed: "Completed"
		}[e.contact_status] || e.contact_status))}</span></td><td class="donor-actions">${a ? `<details class="row-menu"><summary aria-label="${d("Actions")}"><i class="fi fi-rr-angle-small-down menu-chevron" aria-hidden="true"></i></summary><div>${a}</div></details>` : "—"}</td></tr>`;
	}).join("");
	y("contactsRows").innerHTML = n || `<tr><td colspan="7" class="empty">${d("No information")}</td></tr>`, y("contactsTableCount").textContent = `${t.meta.from || 0}–${t.meta.to || 0} / ${t.meta.total}`, y("contactsPagination").innerHTML = N(t.meta, "contacts-page"), t.data.forEach((e, t) => {
		let n = y("contactsRows").rows[t]?.lastElementChild;
		if (!n) return;
		let r = document.createElement("button");
		r.className = "row-btn", r.dataset.action = "contact-preview", r.dataset.id = e.id, r.textContent = d("Preview"), n.querySelector(".row-menu > div")?.prepend(r) ?? n.prepend(r);
	});
}
async function he(e = 1) {
	if (!y("donationsRows")) return;
	let t = await m(`${f.donations}?page=${e}`), n = t.data.map((e, n) => {
		let r = e.bloodDonor?.name || d("No information"), i = r.trim().split(/\s+/u).slice(0, 2).map((e) => Array.from(e)[0]).join("").toLocaleUpperCase(), a = e.donation_status === "verified";
		return `<tr><td>${t.meta.from + n}.</td><td><div class="donorcell"><span class="donoravatar" aria-hidden="true">${p(i)}</span><strong>${p(r)}</strong></div></td><td><span class="table-date">${p(e.donated_at?.slice(0, 10) || d("No information"))}</span></td><td>${p(e.hospital_name || d("No information"))}</td><td>${p(e.bags)}</td><td><span class="status ${a ? "" : "busy"}">${a ? d("Verified") : p(e.donation_status)}</span></td><td class="donor-actions">${e.donation_status === "pending" ? `<button class="row-btn" data-action="verify-donation" data-id="${e.id}">${d("Verify donation")}</button>` : "—"}</td></tr>`;
	}).join("");
	y("donationsRows").innerHTML = n || `<tr><td colspan="7" class="empty">${d("No information")}</td></tr>`, y("donationsTableCount").textContent = `${t.meta.from || 0}–${t.meta.to || 0} / ${t.meta.total}`, y("donationsPagination").innerHTML = N(t.meta, "donations-page"), t.data.forEach((e, t) => {
		let n = y("donationsRows").rows[t]?.lastElementChild;
		if (!n) return;
		let r = document.createElement("button");
		r.className = "row-btn", r.dataset.action = "donation-preview", r.dataset.id = e.id, r.textContent = d("Preview"), n.prepend(r);
	});
}
async function ge() {
	y("groupsRows").innerHTML = b.map((e, t) => {
		let n = S.blood_groups.find((t) => t.id === e.id)?.donors_count ?? 0;
		return `<tr><td>${t + 1}.</td><td><span class="bloodpill">${p(e.name)}</span></td><td>${n} ${d("people")}</td><td><span class="table-date">${p(e.created_at ? new Intl.DateTimeFormat(document.documentElement.lang === "bn" ? "bn-BD" : "en-GB", {
			timeZone: "Asia/Dhaka",
			day: "2-digit",
			month: "short",
			year: "numeric"
		}).format(new Date(e.created_at)) : d("No information"))}</span></td><td>${p(e.creator?.name || (e.created_by ? d("No information") : d("System")))}</td></tr>`;
	}).join("") + (b.length ? "" : `<tr><td colspan="5">${d("No blood groups.")}</td></tr>`);
}
function L(e) {
	window.donationPages?.[e] && window.location.assign(window.donationPages[e]);
}
async function R() {
	try {
		let e = [], t = 1, n = 1;
		do {
			let r = await m(`${f.adminDonors}?page=${t}`);
			e.push(...r.data), n = r.meta.last_page, t++;
		} while (t <= n);
		let r = (e) => "\"" + String(e ?? "").replace(/^[=+@\-\t\r]/, "'$&").replaceAll("\"", "\"\"") + "\"", i = "Name,Blood Group,Area,Phone,Active,Available\n" + e.map((e) => [
			e.name,
			e.bloodGroup?.name,
			e.area,
			e.phone,
			e.status,
			e.is_available
		].map(r).join(",")).join("\n"), a = URL.createObjectURL(new Blob(["﻿", i], { type: "text/csv;charset=utf-8" })), o = document.createElement("a");
		o.href = a, o.download = "blood-donors.csv", o.click(), URL.revokeObjectURL(a);
	} catch (e) {
		_(e.message);
	}
}
y("donorForm").addEventListener("submit", async (e) => {
	e.preventDefault();
	let t = e.submitter;
	if (ie) {
		t && (t.disabled = !0);
		try {
			await ie(Object.fromEntries(new FormData(e.target))), E(), g(oe);
		} catch (e) {
			y("formError").textContent = e.message, _(e.message);
		} finally {
			t && (t.disabled = !1);
		}
	}
}), document.addEventListener("input", (e) => {
	e.target.id === "setting-name" && (y("settingsLiveName").textContent = e.target.value || d("Blood Donation"));
}), document.addEventListener("change", (e) => {
	let t = e.target.closest("#settingsForm input[type=\"file\"]");
	if (!t || !t.files[0]) return;
	let n = t.form.querySelector(`[data-preview="${t.name}"]`), r = new FileReader();
	r.onload = () => {
		n.src = r.result, n.hidden = !1, t.name === "logo" && (y("settingsLiveLogo").src = r.result, y("settingsLiveLogo").hidden = !1);
	}, r.readAsDataURL(t.files[0]);
}), document.addEventListener("submit", async (e) => {
	let t = e.target.closest("#settingsForm");
	if (!t) return;
	e.preventDefault();
	let n = t.querySelector("[type=\"submit\"]");
	if (n.disabled) return;
	let r = t.querySelector(".settings-feedback"), i = [...t.querySelectorAll("input,select,textarea")], a = new FormData(t);
	for (let [e, t] of [...a]) t instanceof File && !t.size && a.delete(e);
	n.disabled = !0, i.forEach((e) => {
		e.disabled = !0;
	}), r.textContent = d("Saving settings…"), r.classList.remove("is-error");
	try {
		let e = await m(f.settings, {
			method: "POST",
			body: a
		});
		t.querySelectorAll("input[type=\"file\"]").forEach((n) => {
			n.value = "";
			let r = t.querySelector(`[data-preview="${n.name}"]`);
			r.src = e.data[n.name + "_url"] || "", r.hidden = !e.data[n.name + "_url"];
		}), r.textContent = d("Settings saved."), g(d("Settings saved."));
	} catch (e) {
		r.textContent = e.message, r.classList.add("is-error"), _(e.message);
	} finally {
		n.disabled = !1, i.forEach((e) => {
			e.disabled = !1;
		});
	}
}), document.addEventListener("click", async (e) => {
	let t = e.target.closest("[data-donor-page]");
	if (t) {
		j(Number(t.dataset.donorPage)).catch((e) => _(e.message));
		return;
	}
	let n = e.target.closest("[data-action]");
	if (!n) return;
	let { action: r, id: i } = n.dataset;
	n.disabled = !0;
	try {
		if (r === "donor-history") {
			let e = await m(`${f.adminDonors}/${i}/history`);
			D(d("Verified donation history"), e.data.map((e) => `<p>${p(e.donated_at?.slice(0, 10))} · ${e.bags} ${d("bags ·")} ${p(e.hospital_name)}</p>`).join("") || d("<p>No verified donations.</p>"), async () => {}), y("donorForm").querySelector("[type=submit]").hidden = !0;
		}
		if (r === "edit-donor" && await fe(i), r === "toggle-donor" || r === "approve-donor") {
			let e = x.find((e) => e.id === Number(i));
			await h(`${f.adminDonors}/${i}${r === "toggle-donor" ? "/availability" : ""}`, r === "toggle-donor" ? { is_available: !e.is_available } : { status: +!e.status }, "PATCH"), g(d(r === "toggle-donor" ? "Availability updated successfully." : e.status ? "Donor marked inactive successfully." : "Donor approved successfully.")), await j(C), await A();
		}
		if (r === "delete-donor" && await v(d("Delete this donor?")) && (await m(`${f.adminDonors}/${i}`, { method: "DELETE" }), g(d("Donor deleted successfully.")), await j(), await A()), r === "verify-request" && (await h(`${f.requests}/${i}/verify`, {}), g(d("Blood request verified successfully.")), await M(), await A(), await me(i)), r === "verify-donation" && (await h(`${f.donations}/${i}/verify`, {}), g(d("Donation verified successfully.")), await he(), await j(), await M(), await A()), r === "donation-preview") {
			let e = (await m(`${f.donations}/${i}`)).data;
			D(d("Donation details"), `<p>${p(e.bloodDonor?.name)} · ${p(e.donated_at?.slice(0, 10))} · ${p(e.hospital_name || d("No information"))} · ${e.bags} ${d("bags")}</p><p>${p(e.notes || d("No notes."))}</p>`, async () => {}), y("donorForm").querySelector("[type=submit]").hidden = !0;
		}
		if (r === "assign-donor" && await P(i), r === "match" && (await h(`${f.requests}/${i}/matches`, {}), g(d("Donor matching completed successfully.")), await A(), await M(), await me(i)), r === "request-details" && await me(i), r === "cancel-request" && await v(d("Cancel this request?")) && (await h(`${f.requests}/${i}`, { request_status: "cancelled" }, "PATCH"), g(d("Blood request cancelled successfully.")), await M(), await A()), r === "record-donation" && F(i, n.dataset.request), r === "new-donation" && F(), r === "respond") {
			let e = n.dataset.request;
			D(d("Donor response"), d("<div class=\"field\"><select name=\"response_status\"><option value=\"contacted\">Contacted</option><option value=\"accepted\">Accepted</option><option value=\"declined\">Declined</option></select></div>") + O(d("Notes"), "notes", "text", "", !1), async (t) => {
				await h(`${f.requests}/${e}/matches/${i}`, t, "PATCH");
			});
		}
		if (r === "contact-preview") {
			let e = (await m(`${f.contacts}/${i}`)).data;
			D(d("Contact request details"), `<p>${p(e.requester_name)} · ${p(e.requester_phone)}</p><p>${p(e.blood_donor?.name || d("No information"))} · ${p(e.contact_status)}</p><p>${p(e.message || d("No information"))}</p>`, async () => {}), y("donorForm").querySelector("[type=submit]").hidden = !0;
		}
		r.startsWith("contact-") && (await h(`${f.contacts}/${i}`, { contact_status: r.slice(8) }, "PATCH"), g(d("Contact request status updated successfully.")), await I()), r === "requests-page" && await M(Number(i)), r === "contacts-page" && await I(Number(i)), r === "donations-page" && await he(Number(i));
	} catch (e) {
		_(e.message);
	} finally {
		n.disabled = !1;
	}
}), Object.assign(window, {
	showToast: ce,
	closeModal: E,
	openModal: de,
	selectPage: L,
	renderTable: ue,
	drawChart: le,
	exportCSV: R,
	openRequestForm: pe
}), y("today").textContent = new Intl.DateTimeFormat(document.documentElement.lang === "bn" ? "bn-BD" : "en-GB", {
	timeZone: "Asia/Dhaka",
	day: "numeric",
	month: "long",
	year: "numeric"
}).format(/* @__PURE__ */ new Date()), y("weekday").textContent = new Intl.DateTimeFormat(document.documentElement.lang === "bn" ? "bn-BD" : "en-GB", {
	timeZone: "Asia/Dhaka",
	weekday: "long"
}).format(/* @__PURE__ */ new Date()), y("globalSearch").value = new URL(window.location.href).searchParams.get("q") || "", y("search") && (y("search").value = y("globalSearch").value), y("globalSearch").addEventListener("keydown", (e) => {
	if (e.key !== "Enter") return;
	e.preventDefault();
	let t = new URL(window.donationPages.donors, window.location.origin);
	e.target.value.trim() && t.searchParams.set("q", e.target.value.trim()), window.location.assign(t.href);
}), y("globalSearch").addEventListener("input", (e) => {
	y("search") && (y("search").value = e.target.value, ue());
}), l();
try {
	[
		"dashboard",
		"donors",
		"requests",
		"donations",
		"groups"
	].includes(w) && (b = (await m(f.groups)).data), w !== "settings" && await A(), ["dashboard", "donors"].includes(w) && await j(), w === "requests" && await M(), w === "contacts" && await I(), w === "donations" && await he(), w === "groups" && await ge(), w === "reports" && (y("pageSubtitle").textContent = `${S.donations} ${d("donations ·")} ${S.bags_donated} ${d("bags of blood")}`);
} catch (e) {
	_(e.message);
}
function z() {
	document.querySelectorAll(".row-menu[open]").forEach((e) => {
		e.open = !1;
	});
}
document.addEventListener("click", (e) => {
	let t = e.target.closest(".row-menu > summary");
	if (!t) {
		z();
		return;
	}
	e.preventDefault();
	let n = t.parentElement, r = n.open;
	if (z(), r) return;
	n.open = !0;
	let i = n.querySelector(":scope > div"), a = t.getBoundingClientRect(), o = i.getBoundingClientRect(), s = Math.max(8, Math.min(a.right - o.width, window.innerWidth - o.width - 8)), c = a.bottom + o.height + 8 <= window.innerHeight ? a.bottom + 6 : Math.max(8, a.top - o.height - 6);
	i.style.left = `${s}px`, i.style.top = `${c}px`;
}), document.addEventListener("keydown", (e) => {
	e.key === "Escape" && z();
}), document.addEventListener("scroll", (e) => {
	e.target instanceof Element && e.target.closest(".row-menu > div") || z();
}, !0), window.addEventListener("resize", z), y("modalOverlay").addEventListener("keydown", (e) => {
	if (e.key === "Escape") {
		E();
		return;
	}
	if (e.key !== "Tab") return;
	let t = [...y("modalOverlay").querySelectorAll("button, a[href], input, select, textarea, summary, [tabindex=\"0\"]")].filter((e) => !e.disabled && e.getClientRects().length), n = t[0], r = t.at(-1);
	e.shiftKey && document.activeElement === n && (e.preventDefault(), r?.focus()), !e.shiftKey && document.activeElement === r && (e.preventDefault(), n?.focus());
}), document.addEventListener("input", (e) => {
	if (e.target.id !== "matchedDonorSearch") return;
	let t = e.target.value.trim().toLocaleLowerCase(), n = [...document.querySelectorAll("#matchedDonorList .request-donor")];
	n.forEach((e) => {
		e.hidden = !e.querySelector(".request-donor-info").textContent.toLocaleLowerCase().includes(t);
	}), y("matchedDonorEmpty").hidden = !n.length || n.some((e) => !e.hidden);
});
