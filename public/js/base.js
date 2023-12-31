//==============================================================================
// Extend JS
//==============================================================================
//     Underscore.js 1.9.1
//     http://underscorejs.org
//     (c) 2009-2018 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
//     Underscore may be freely distributed under the MIT license.
!function () { var n = "object" == typeof self && self.self === self && self || "object" == typeof global && global.global === global && global || this || {}, r = n._, e = Array.prototype, o = Object.prototype, s = "undefined" != typeof Symbol ? Symbol.prototype : null, u = e.push, c = e.slice, p = o.toString, i = o.hasOwnProperty, t = Array.isArray, a = Object.keys, l = Object.create, f = function () { }, h = function (n) { return n instanceof h ? n : this instanceof h ? void (this._wrapped = n) : new h(n) }; "undefined" == typeof exports || exports.nodeType ? n._ = h : ("undefined" != typeof module && !module.nodeType && module.exports && (exports = module.exports = h), exports._ = h), h.VERSION = "1.9.1"; var v, y = function (u, i, n) { if (void 0 === i) return u; switch (null == n ? 3 : n) { case 1: return function (n) { return u.call(i, n) }; case 3: return function (n, r, t) { return u.call(i, n, r, t) }; case 4: return function (n, r, t, e) { return u.call(i, n, r, t, e) } }return function () { return u.apply(i, arguments) } }, d = function (n, r, t) { return h.iteratee !== v ? h.iteratee(n, r) : null == n ? h.identity : h.isFunction(n) ? y(n, r, t) : h.isObject(n) && !h.isArray(n) ? h.matcher(n) : h.property(n) }; h.iteratee = v = function (n, r) { return d(n, r, 1 / 0) }; var g = function (u, i) { return i = null == i ? u.length - 1 : +i, function () { for (var n = Math.max(arguments.length - i, 0), r = Array(n), t = 0; t < n; t++)r[t] = arguments[t + i]; switch (i) { case 0: return u.call(this, r); case 1: return u.call(this, arguments[0], r); case 2: return u.call(this, arguments[0], arguments[1], r) }var e = Array(i + 1); for (t = 0; t < i; t++)e[t] = arguments[t]; return e[i] = r, u.apply(this, e) } }, m = function (n) { if (!h.isObject(n)) return {}; if (l) return l(n); f.prototype = n; var r = new f; return f.prototype = null, r }, b = function (r) { return function (n) { return null == n ? void 0 : n[r] } }, j = function (n, r) { return null != n && i.call(n, r) }, x = function (n, r) { for (var t = r.length, e = 0; e < t; e++) { if (null == n) return; n = n[r[e]] } return t ? n : void 0 }, _ = Math.pow(2, 53) - 1, A = b("length"), w = function (n) { var r = A(n); return "number" == typeof r && 0 <= r && r <= _ }; h.each = h.forEach = function (n, r, t) { var e, u; if (r = y(r, t), w(n)) for (e = 0, u = n.length; e < u; e++)r(n[e], e, n); else { var i = h.keys(n); for (e = 0, u = i.length; e < u; e++)r(n[i[e]], i[e], n) } return n }, h.map = h.collect = function (n, r, t) { r = d(r, t); for (var e = !w(n) && h.keys(n), u = (e || n).length, i = Array(u), o = 0; o < u; o++) { var a = e ? e[o] : o; i[o] = r(n[a], a, n) } return i }; var O = function (c) { return function (n, r, t, e) { var u = 3 <= arguments.length; return function (n, r, t, e) { var u = !w(n) && h.keys(n), i = (u || n).length, o = 0 < c ? 0 : i - 1; for (e || (t = n[u ? u[o] : o], o += c); 0 <= o && o < i; o += c) { var a = u ? u[o] : o; t = r(t, n[a], a, n) } return t }(n, y(r, e, 4), t, u) } }; h.reduce = h.foldl = h.inject = O(1), h.reduceRight = h.foldr = O(-1), h.find = h.detect = function (n, r, t) { var e = (w(n) ? h.findIndex : h.findKey)(n, r, t); if (void 0 !== e && -1 !== e) return n[e] }, h.filter = h.select = function (n, e, r) { var u = []; return e = d(e, r), h.each(n, function (n, r, t) { e(n, r, t) && u.push(n) }), u }, h.reject = function (n, r, t) { return h.filter(n, h.negate(d(r)), t) }, h.every = h.all = function (n, r, t) { r = d(r, t); for (var e = !w(n) && h.keys(n), u = (e || n).length, i = 0; i < u; i++) { var o = e ? e[i] : i; if (!r(n[o], o, n)) return !1 } return !0 }, h.some = h.any = function (n, r, t) { r = d(r, t); for (var e = !w(n) && h.keys(n), u = (e || n).length, i = 0; i < u; i++) { var o = e ? e[i] : i; if (r(n[o], o, n)) return !0 } return !1 }, h.contains = h.includes = h.include = function (n, r, t, e) { return w(n) || (n = h.values(n)), ("number" != typeof t || e) && (t = 0), 0 <= h.indexOf(n, r, t) }, h.invoke = g(function (n, t, e) { var u, i; return h.isFunction(t) ? i = t : h.isArray(t) && (u = t.slice(0, -1), t = t[t.length - 1]), h.map(n, function (n) { var r = i; if (!r) { if (u && u.length && (n = x(n, u)), null == n) return; r = n[t] } return null == r ? r : r.apply(n, e) }) }), h.pluck = function (n, r) { return h.map(n, h.property(r)) }, h.where = function (n, r) { return h.filter(n, h.matcher(r)) }, h.findWhere = function (n, r) { return h.find(n, h.matcher(r)) }, h.max = function (n, e, r) { var t, u, i = -1 / 0, o = -1 / 0; if (null == e || "number" == typeof e && "object" != typeof n[0] && null != n) for (var a = 0, c = (n = w(n) ? n : h.values(n)).length; a < c; a++)null != (t = n[a]) && i < t && (i = t); else e = d(e, r), h.each(n, function (n, r, t) { u = e(n, r, t), (o < u || u === -1 / 0 && i === -1 / 0) && (i = n, o = u) }); return i }, h.min = function (n, e, r) { var t, u, i = 1 / 0, o = 1 / 0; if (null == e || "number" == typeof e && "object" != typeof n[0] && null != n) for (var a = 0, c = (n = w(n) ? n : h.values(n)).length; a < c; a++)null != (t = n[a]) && t < i && (i = t); else e = d(e, r), h.each(n, function (n, r, t) { ((u = e(n, r, t)) < o || u === 1 / 0 && i === 1 / 0) && (i = n, o = u) }); return i }, h.shuffle = function (n) { return h.sample(n, 1 / 0) }, h.sample = function (n, r, t) { if (null == r || t) return w(n) || (n = h.values(n)), n[h.random(n.length - 1)]; var e = w(n) ? h.clone(n) : h.values(n), u = A(e); r = Math.max(Math.min(r, u), 0); for (var i = u - 1, o = 0; o < r; o++) { var a = h.random(o, i), c = e[o]; e[o] = e[a], e[a] = c } return e.slice(0, r) }, h.sortBy = function (n, e, r) { var u = 0; return e = d(e, r), h.pluck(h.map(n, function (n, r, t) { return { value: n, index: u++, criteria: e(n, r, t) } }).sort(function (n, r) { var t = n.criteria, e = r.criteria; if (t !== e) { if (e < t || void 0 === t) return 1; if (t < e || void 0 === e) return -1 } return n.index - r.index }), "value") }; var k = function (o, r) { return function (e, u, n) { var i = r ? [[], []] : {}; return u = d(u, n), h.each(e, function (n, r) { var t = u(n, r, e); o(i, n, t) }), i } }; h.groupBy = k(function (n, r, t) { j(n, t) ? n[t].push(r) : n[t] = [r] }), h.indexBy = k(function (n, r, t) { n[t] = r }), h.countBy = k(function (n, r, t) { j(n, t) ? n[t]++ : n[t] = 1 }); var S = /[^\ud800-\udfff]|[\ud800-\udbff][\udc00-\udfff]|[\ud800-\udfff]/g; h.toArray = function (n) { return n ? h.isArray(n) ? c.call(n) : h.isString(n) ? n.match(S) : w(n) ? h.map(n, h.identity) : h.values(n) : [] }, h.size = function (n) { return null == n ? 0 : w(n) ? n.length : h.keys(n).length }, h.partition = k(function (n, r, t) { n[t ? 0 : 1].push(r) }, !0), h.first = h.head = h.take = function (n, r, t) { return null == n || n.length < 1 ? null == r ? void 0 : [] : null == r || t ? n[0] : h.initial(n, n.length - r) }, h.initial = function (n, r, t) { return c.call(n, 0, Math.max(0, n.length - (null == r || t ? 1 : r))) }, h.last = function (n, r, t) { return null == n || n.length < 1 ? null == r ? void 0 : [] : null == r || t ? n[n.length - 1] : h.rest(n, Math.max(0, n.length - r)) }, h.rest = h.tail = h.drop = function (n, r, t) { return c.call(n, null == r || t ? 1 : r) }, h.compact = function (n) { return h.filter(n, Boolean) }; var M = function (n, r, t, e) { for (var u = (e = e || []).length, i = 0, o = A(n); i < o; i++) { var a = n[i]; if (w(a) && (h.isArray(a) || h.isArguments(a))) if (r) for (var c = 0, l = a.length; c < l;)e[u++] = a[c++]; else M(a, r, t, e), u = e.length; else t || (e[u++] = a) } return e }; h.flatten = function (n, r) { return M(n, r, !1) }, h.without = g(function (n, r) { return h.difference(n, r) }), h.uniq = h.unique = function (n, r, t, e) { h.isBoolean(r) || (e = t, t = r, r = !1), null != t && (t = d(t, e)); for (var u = [], i = [], o = 0, a = A(n); o < a; o++) { var c = n[o], l = t ? t(c, o, n) : c; r && !t ? (o && i === l || u.push(c), i = l) : t ? h.contains(i, l) || (i.push(l), u.push(c)) : h.contains(u, c) || u.push(c) } return u }, h.union = g(function (n) { return h.uniq(M(n, !0, !0)) }), h.intersection = function (n) { for (var r = [], t = arguments.length, e = 0, u = A(n); e < u; e++) { var i = n[e]; if (!h.contains(r, i)) { var o; for (o = 1; o < t && h.contains(arguments[o], i); o++); o === t && r.push(i) } } return r }, h.difference = g(function (n, r) { return r = M(r, !0, !0), h.filter(n, function (n) { return !h.contains(r, n) }) }), h.unzip = function (n) { for (var r = n && h.max(n, A).length || 0, t = Array(r), e = 0; e < r; e++)t[e] = h.pluck(n, e); return t }, h.zip = g(h.unzip), h.object = function (n, r) { for (var t = {}, e = 0, u = A(n); e < u; e++)r ? t[n[e]] = r[e] : t[n[e][0]] = n[e][1]; return t }; var F = function (i) { return function (n, r, t) { r = d(r, t); for (var e = A(n), u = 0 < i ? 0 : e - 1; 0 <= u && u < e; u += i)if (r(n[u], u, n)) return u; return -1 } }; h.findIndex = F(1), h.findLastIndex = F(-1), h.sortedIndex = function (n, r, t, e) { for (var u = (t = d(t, e, 1))(r), i = 0, o = A(n); i < o;) { var a = Math.floor((i + o) / 2); t(n[a]) < u ? i = a + 1 : o = a } return i }; var E = function (i, o, a) { return function (n, r, t) { var e = 0, u = A(n); if ("number" == typeof t) 0 < i ? e = 0 <= t ? t : Math.max(t + u, e) : u = 0 <= t ? Math.min(t + 1, u) : t + u + 1; else if (a && t && u) return n[t = a(n, r)] === r ? t : -1; if (r != r) return 0 <= (t = o(c.call(n, e, u), h.isNaN)) ? t + e : -1; for (t = 0 < i ? e : u - 1; 0 <= t && t < u; t += i)if (n[t] === r) return t; return -1 } }; h.indexOf = E(1, h.findIndex, h.sortedIndex), h.lastIndexOf = E(-1, h.findLastIndex), h.range = function (n, r, t) { null == r && (r = n || 0, n = 0), t || (t = r < n ? -1 : 1); for (var e = Math.max(Math.ceil((r - n) / t), 0), u = Array(e), i = 0; i < e; i++ , n += t)u[i] = n; return u }, h.chunk = function (n, r) { if (null == r || r < 1) return []; for (var t = [], e = 0, u = n.length; e < u;)t.push(c.call(n, e, e += r)); return t }; var N = function (n, r, t, e, u) { if (!(e instanceof r)) return n.apply(t, u); var i = m(n.prototype), o = n.apply(i, u); return h.isObject(o) ? o : i }; h.bind = g(function (r, t, e) { if (!h.isFunction(r)) throw new TypeError("Bind must be called on a function"); var u = g(function (n) { return N(r, u, t, this, e.concat(n)) }); return u }), h.partial = g(function (u, i) { var o = h.partial.placeholder, a = function () { for (var n = 0, r = i.length, t = Array(r), e = 0; e < r; e++)t[e] = i[e] === o ? arguments[n++] : i[e]; for (; n < arguments.length;)t.push(arguments[n++]); return N(u, a, this, this, t) }; return a }), (h.partial.placeholder = h).bindAll = g(function (n, r) { var t = (r = M(r, !1, !1)).length; if (t < 1) throw new Error("bindAll must be passed function names"); for (; t--;) { var e = r[t]; n[e] = h.bind(n[e], n) } }), h.memoize = function (e, u) { var i = function (n) { var r = i.cache, t = "" + (u ? u.apply(this, arguments) : n); return j(r, t) || (r[t] = e.apply(this, arguments)), r[t] }; return i.cache = {}, i }, h.delay = g(function (n, r, t) { return setTimeout(function () { return n.apply(null, t) }, r) }), h.defer = h.partial(h.delay, h, 1), h.throttle = function (t, e, u) { var i, o, a, c, l = 0; u || (u = {}); var f = function () { l = !1 === u.leading ? 0 : h.now(), i = null, c = t.apply(o, a), i || (o = a = null) }, n = function () { var n = h.now(); l || !1 !== u.leading || (l = n); var r = e - (n - l); return o = this, a = arguments, r <= 0 || e < r ? (i && (clearTimeout(i), i = null), l = n, c = t.apply(o, a), i || (o = a = null)) : i || !1 === u.trailing || (i = setTimeout(f, r)), c }; return n.cancel = function () { clearTimeout(i), l = 0, i = o = a = null }, n }, h.debounce = function (t, e, u) { var i, o, a = function (n, r) { i = null, r && (o = t.apply(n, r)) }, n = g(function (n) { if (i && clearTimeout(i), u) { var r = !i; i = setTimeout(a, e), r && (o = t.apply(this, n)) } else i = h.delay(a, e, this, n); return o }); return n.cancel = function () { clearTimeout(i), i = null }, n }, h.wrap = function (n, r) { return h.partial(r, n) }, h.negate = function (n) { return function () { return !n.apply(this, arguments) } }, h.compose = function () { var t = arguments, e = t.length - 1; return function () { for (var n = e, r = t[e].apply(this, arguments); n--;)r = t[n].call(this, r); return r } }, h.after = function (n, r) { return function () { if (--n < 1) return r.apply(this, arguments) } }, h.before = function (n, r) { var t; return function () { return 0 < --n && (t = r.apply(this, arguments)), n <= 1 && (r = null), t } }, h.once = h.partial(h.before, 2), h.restArguments = g; var I = !{ toString: null }.propertyIsEnumerable("toString"), T = ["valueOf", "isPrototypeOf", "toString", "propertyIsEnumerable", "hasOwnProperty", "toLocaleString"], B = function (n, r) { var t = T.length, e = n.constructor, u = h.isFunction(e) && e.prototype || o, i = "constructor"; for (j(n, i) && !h.contains(r, i) && r.push(i); t--;)(i = T[t]) in n && n[i] !== u[i] && !h.contains(r, i) && r.push(i) }; h.keys = function (n) { if (!h.isObject(n)) return []; if (a) return a(n); var r = []; for (var t in n) j(n, t) && r.push(t); return I && B(n, r), r }, h.allKeys = function (n) { if (!h.isObject(n)) return []; var r = []; for (var t in n) r.push(t); return I && B(n, r), r }, h.values = function (n) { for (var r = h.keys(n), t = r.length, e = Array(t), u = 0; u < t; u++)e[u] = n[r[u]]; return e }, h.mapObject = function (n, r, t) { r = d(r, t); for (var e = h.keys(n), u = e.length, i = {}, o = 0; o < u; o++) { var a = e[o]; i[a] = r(n[a], a, n) } return i }, h.pairs = function (n) { for (var r = h.keys(n), t = r.length, e = Array(t), u = 0; u < t; u++)e[u] = [r[u], n[r[u]]]; return e }, h.invert = function (n) { for (var r = {}, t = h.keys(n), e = 0, u = t.length; e < u; e++)r[n[t[e]]] = t[e]; return r }, h.functions = h.methods = function (n) { var r = []; for (var t in n) h.isFunction(n[t]) && r.push(t); return r.sort() }; var R = function (c, l) { return function (n) { var r = arguments.length; if (l && (n = Object(n)), r < 2 || null == n) return n; for (var t = 1; t < r; t++)for (var e = arguments[t], u = c(e), i = u.length, o = 0; o < i; o++) { var a = u[o]; l && void 0 !== n[a] || (n[a] = e[a]) } return n } }; h.extend = R(h.allKeys), h.extendOwn = h.assign = R(h.keys), h.findKey = function (n, r, t) { r = d(r, t); for (var e, u = h.keys(n), i = 0, o = u.length; i < o; i++)if (r(n[e = u[i]], e, n)) return e }; var q, K, z = function (n, r, t) { return r in t }; h.pick = g(function (n, r) { var t = {}, e = r[0]; if (null == n) return t; h.isFunction(e) ? (1 < r.length && (e = y(e, r[1])), r = h.allKeys(n)) : (e = z, r = M(r, !1, !1), n = Object(n)); for (var u = 0, i = r.length; u < i; u++) { var o = r[u], a = n[o]; e(a, o, n) && (t[o] = a) } return t }), h.omit = g(function (n, t) { var r, e = t[0]; return h.isFunction(e) ? (e = h.negate(e), 1 < t.length && (r = t[1])) : (t = h.map(M(t, !1, !1), String), e = function (n, r) { return !h.contains(t, r) }), h.pick(n, e, r) }), h.defaults = R(h.allKeys, !0), h.create = function (n, r) { var t = m(n); return r && h.extendOwn(t, r), t }, h.clone = function (n) { return h.isObject(n) ? h.isArray(n) ? n.slice() : h.extend({}, n) : n }, h.tap = function (n, r) { return r(n), n }, h.isMatch = function (n, r) { var t = h.keys(r), e = t.length; if (null == n) return !e; for (var u = Object(n), i = 0; i < e; i++) { var o = t[i]; if (r[o] !== u[o] || !(o in u)) return !1 } return !0 }, q = function (n, r, t, e) { if (n === r) return 0 !== n || 1 / n == 1 / r; if (null == n || null == r) return !1; if (n != n) return r != r; var u = typeof n; return ("function" === u || "object" === u || "object" == typeof r) && K(n, r, t, e) }, K = function (n, r, t, e) { n instanceof h && (n = n._wrapped), r instanceof h && (r = r._wrapped); var u = p.call(n); if (u !== p.call(r)) return !1; switch (u) { case "[object RegExp]": case "[object String]": return "" + n == "" + r; case "[object Number]": return +n != +n ? +r != +r : 0 == +n ? 1 / +n == 1 / r : +n == +r; case "[object Date]": case "[object Boolean]": return +n == +r; case "[object Symbol]": return s.valueOf.call(n) === s.valueOf.call(r) }var i = "[object Array]" === u; if (!i) { if ("object" != typeof n || "object" != typeof r) return !1; var o = n.constructor, a = r.constructor; if (o !== a && !(h.isFunction(o) && o instanceof o && h.isFunction(a) && a instanceof a) && "constructor" in n && "constructor" in r) return !1 } e = e || []; for (var c = (t = t || []).length; c--;)if (t[c] === n) return e[c] === r; if (t.push(n), e.push(r), i) { if ((c = n.length) !== r.length) return !1; for (; c--;)if (!q(n[c], r[c], t, e)) return !1 } else { var l, f = h.keys(n); if (c = f.length, h.keys(r).length !== c) return !1; for (; c--;)if (l = f[c], !j(r, l) || !q(n[l], r[l], t, e)) return !1 } return t.pop(), e.pop(), !0 }, h.isEqual = function (n, r) { return q(n, r) }, h.isEmpty = function (n) { return null == n || (w(n) && (h.isArray(n) || h.isString(n) || h.isArguments(n)) ? 0 === n.length : 0 === h.keys(n).length) }, h.isElement = function (n) { return !(!n || 1 !== n.nodeType) }, h.isArray = t || function (n) { return "[object Array]" === p.call(n) }, h.isObject = function (n) { var r = typeof n; return "function" === r || "object" === r && !!n }, h.each(["Arguments", "Function", "String", "Number", "Date", "RegExp", "Error", "Symbol", "Map", "WeakMap", "Set", "WeakSet"], function (r) { h["is" + r] = function (n) { return p.call(n) === "[object " + r + "]" } }), h.isArguments(arguments) || (h.isArguments = function (n) { return j(n, "callee") }); var D = n.document && n.document.childNodes; "function" != typeof /./ && "object" != typeof Int8Array && "function" != typeof D && (h.isFunction = function (n) { return "function" == typeof n || !1 }), h.isFinite = function (n) { return !h.isSymbol(n) && isFinite(n) && !isNaN(parseFloat(n)) }, h.isNaN = function (n) { return h.isNumber(n) && isNaN(n) }, h.isBoolean = function (n) { return !0 === n || !1 === n || "[object Boolean]" === p.call(n) }, h.isNull = function (n) { return null === n }, h.isUndefined = function (n) { return void 0 === n }, h.has = function (n, r) { if (!h.isArray(r)) return j(n, r); for (var t = r.length, e = 0; e < t; e++) { var u = r[e]; if (null == n || !i.call(n, u)) return !1; n = n[u] } return !!t }, h.noConflict = function () { return n._ = r, this }, h.identity = function (n) { return n }, h.constant = function (n) { return function () { return n } }, h.noop = function () { }, h.property = function (r) { return h.isArray(r) ? function (n) { return x(n, r) } : b(r) }, h.propertyOf = function (r) { return null == r ? function () { } : function (n) { return h.isArray(n) ? x(r, n) : r[n] } }, h.matcher = h.matches = function (r) { return r = h.extendOwn({}, r), function (n) { return h.isMatch(n, r) } }, h.times = function (n, r, t) { var e = Array(Math.max(0, n)); r = y(r, t, 1); for (var u = 0; u < n; u++)e[u] = r(u); return e }, h.random = function (n, r) { return null == r && (r = n, n = 0), n + Math.floor(Math.random() * (r - n + 1)) }, h.now = Date.now || function () { return (new Date).getTime() }; var L = { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#x27;", "`": "&#x60;" }, P = h.invert(L), W = function (r) { var t = function (n) { return r[n] }, n = "(?:" + h.keys(r).join("|") + ")", e = RegExp(n), u = RegExp(n, "g"); return function (n) { return n = null == n ? "" : "" + n, e.test(n) ? n.replace(u, t) : n } }; h.escape = W(L), h.unescape = W(P), h.result = function (n, r, t) { h.isArray(r) || (r = [r]); var e = r.length; if (!e) return h.isFunction(t) ? t.call(n) : t; for (var u = 0; u < e; u++) { var i = null == n ? void 0 : n[r[u]]; void 0 === i && (i = t, u = e), n = h.isFunction(i) ? i.call(n) : i } return n }; var C = 0; h.uniqueId = function (n) { var r = ++C + ""; return n ? n + r : r }, h.templateSettings = { evaluate: /<%([\s\S]+?)%>/g, interpolate: /<%=([\s\S]+?)%>/g, escape: /<%-([\s\S]+?)%>/g }; var J = /(.)^/, U = { "'": "'", "\\": "\\", "\r": "r", "\n": "n", "\u2028": "u2028", "\u2029": "u2029" }, V = /\\|'|\r|\n|\u2028|\u2029/g, $ = function (n) { return "\\" + U[n] }; h.template = function (i, n, r) { !n && r && (n = r), n = h.defaults({}, n, h.templateSettings); var t, e = RegExp([(n.escape || J).source, (n.interpolate || J).source, (n.evaluate || J).source].join("|") + "|$", "g"), o = 0, a = "__p+='"; i.replace(e, function (n, r, t, e, u) { return a += i.slice(o, u).replace(V, $), o = u + n.length, r ? a += "'+\n((__t=(" + r + "))==null?'':_.escape(__t))+\n'" : t ? a += "'+\n((__t=(" + t + "))==null?'':__t)+\n'" : e && (a += "';\n" + e + "\n__p+='"), n }), a += "';\n", n.variable || (a = "with(obj||{}){\n" + a + "}\n"), a = "var __t,__p='',__j=Array.prototype.join," + "print=function(){__p+=__j.call(arguments,'');};\n" + a + "return __p;\n"; try { t = new Function(n.variable || "obj", "_", a) } catch (n) { throw n.source = a, n } var u = function (n) { return t.call(this, n, h) }, c = n.variable || "obj"; return u.source = "function(" + c + "){\n" + a + "}", u }, h.chain = function (n) { var r = h(n); return r._chain = !0, r }; var G = function (n, r) { return n._chain ? h(r).chain() : r }; h.mixin = function (t) { return h.each(h.functions(t), function (n) { var r = h[n] = t[n]; h.prototype[n] = function () { var n = [this._wrapped]; return u.apply(n, arguments), G(this, r.apply(h, n)) } }), h }, h.mixin(h), h.each(["pop", "push", "reverse", "shift", "sort", "splice", "unshift"], function (r) { var t = e[r]; h.prototype[r] = function () { var n = this._wrapped; return t.apply(n, arguments), "shift" !== r && "splice" !== r || 0 !== n.length || delete n[0], G(this, n) } }), h.each(["concat", "join", "slice"], function (n) { var r = e[n]; h.prototype[n] = function () { return G(this, r.apply(this._wrapped, arguments)) } }), h.prototype.value = function () { return this._wrapped }, h.prototype.valueOf = h.prototype.toJSON = h.prototype.value, h.prototype.toString = function () { return String(this._wrapped) }, "function" == typeof define && define.amd && define("underscore", [], function () { return h }) }();
// https://unpkg.com/ulid@2.3.0/dist/index.umd.js
!function(r,e){"object"==typeof exports&&"undefined"!=typeof module?e(exports):"function"==typeof define&&define.amd?define(["exports"],e):e(r.ULID={})}(this,function(r){"use strict";function e(r){var e=new Error(r);return e.source="ulid",e}var t="0123456789ABCDEFGHJKMNPQRSTVWXYZ",n=t.length,o=Math.pow(2,48)-1,i=10,u=16;function a(r,e,t){return e>r.length-1?r:r.substr(0,e)+t+r.substr(e+1)}function c(r){for(var o=void 0,i=r.length,u=void 0,c=void 0,f=n-1;!o&&i-- >=0;){if(u=r[i],-1===(c=t.indexOf(u)))throw e("incorrectly encoded string");c!==f?o=a(r,i,t[c+1]):r=a(r,i,t[0])}if("string"==typeof o)return o;throw e("cannot increment this string")}function f(r){var e=Math.floor(r()*n);return e===n&&(e=n-1),t.charAt(e)}function d(r,i){if(isNaN(r))throw new Error(r+" must be a number");if(r>o)throw e("cannot encode time greater than "+o);if(r<0)throw e("time must be positive");if(!1===Number.isInteger(r))throw e("time must be an integer");for(var u=void 0,a="";i>0;i--)u=r%n,a=t.charAt(u)+a,r=(r-u)/n;return a}function s(r,e){for(var t="";r>0;r--)t=f(e)+t;return t}function h(){var r=arguments.length>0&&void 0!==arguments[0]&&arguments[0],t=arguments[1];t||(t="undefined"!=typeof window?window:null);var n=t&&(t.crypto||t.msCrypto);if(n)return function(){var r=new Uint8Array(1);return n.getRandomValues(r),r[0]/255};try{var o=require("crypto");return function(){return o.randomBytes(1).readUInt8()/255}}catch(r){}if(r){try{console.error("secure crypto unusable, falling back to insecure Math.random()!")}catch(r){}return function(){return Math.random()}}throw e("secure crypto unusable, insecure Math.random not allowed")}function l(r){return r||(r=h()),function(e){return isNaN(e)&&(e=Date.now()),d(e,i)+s(u,r)}}var m=l();r.replaceCharAt=a,r.incrementBase32=c,r.randomChar=f,r.encodeTime=d,r.encodeRandom=s,r.decodeTime=function(r){if(r.length!==i+u)throw e("malformed ulid");var a=r.substr(0,i).split("").reverse().reduce(function(r,o,i){var u=t.indexOf(o);if(-1===u)throw e("invalid character found: "+o);return r+u*Math.pow(n,i)},0);if(a>o)throw e("malformed ulid, timestamp too large");return a},r.detectPrng=h,r.factory=l,r.monotonicFactory=function(r){r||(r=h());var e=0,t=void 0;return function(n){if(isNaN(n)&&(n=Date.now()),n<=e){var o=t=c(t);return d(e,i)+o}e=n;var a=t=s(u,r);return d(n,i)+a}},r.ulid=m,Object.defineProperty(r,"__esModule",{value:!0})});
////
Node.prototype.hasClass = function (className) {
    if (this.classList) {
        return this.classList.contains(className);
    } else {
        return (-1 < this.className.indexOf(className));
    }
};

Node.prototype.addClass = function (className) {
    if (this.classList) {
        this.classList.add(className);
    } else if (!this.hasClass(className)) {
        //var classes = this.className.split(" ");
        //classes.push(className);
        //this.className = classes.join(" ");
        this.className += " " + className;
    }
    return this;
};

Node.prototype.removeClass = function (className) {
    if (this.classList) {
        this.classList.remove(className);
    } else {
        //var classes = this.className.split(" ");
        //classes.splice(classes.indexOf(className), 1);
        //this.className = classes.join(" ");
        this.className = this.className.replace(new RegExp('(?:^|\\s)' + className + '(?!\\S)'), '');
    }
    return this;
};

Node.prototype.toggleClass = function (className) {
    if (this.classList) {
        this.classList.toggle(className);
    } else {
        if (this.hasClass(className)) {
            this.removeClass(className);
        } else {
            this.addClass(className);
        }
    }
    return this;
};

// emulamos el "is" de jquery
Node.prototype.is = function (selector) {
    if (selector.nodeType) {
        return this === selector;
    }

    var qa = (typeof (selector) === 'string' ? document.querySelectorAll(selector) : selector),
        length = qa.length,
        returnArr = [];

    while (length--) {
        if (qa[length] === this) {
            return true;
        }
    }
    return false;
}

Node.prototype.findAncestor = function (className) {
    var current = this.parentElement;
    while (current !== null && !current.hasClass(className)) {
        current = current.parentElement;
    }
    return current;
}

/* Adds Element BEFORE NeighborElement */
Element.prototype.appendBefore = function (element) {
    element.parentNode.insertBefore(this, element);
}, false;

/* Adds Element AFTER NeighborElement */
Element.prototype.appendAfter = function (element) {
    element.parentNode.insertBefore(this, element.nextSibling);
}, false;

// Polyfill isArray
if (typeof Array.isArray === 'undefined') {
    Array.isArray = function (obj) {
        return Object.prototype.toString.call(obj) === '[object Array]';
    }
};

function imgToSvg(selector = 'img.svg') {
    document.querySelectorAll(selector).forEach(img => {
        fetch(img.src).then(response => response.text()).then(text => {
            const parser = new DOMParser();
            const xmlDoc = parser.parseFromString(text, "text/xml");

            const svg = xmlDoc.getElementsByTagName('svg')[0];

            if (typeof img.id !== 'undefined') {
                svg.setAttribute('id', img.id);
            }
            if (typeof img.className !== 'undefined') {
                svg.setAttribute('class', img.className + ' replaced-svg');
            }

            svg.removeAttribute('xmlns:a'); // Remove any invalid XML tags as per http://validator.w3.org

            // Check if the viewport is set, if the viewport is not set the SVG wont't scale.
            if (!svg.getAttribute('viewBox') && svg.getAttribute('height') && svg.getAttribute('width')) {
                svg.setAttribute('viewBox', '0 0 ' + svg.getAttribute('height') + ' ' + svg.getAttribute('width'))
            }

            img.parentNode.replaceChild(svg, img);
        });
    });
}


// https://github.com/muicss/js - v3.5.5
const loadjs = function () {
    var l = function () { }, c = {}, f = {}, u = {};
    function o(e, n) {
        if (e) {
            var t = u[e];
            if (f[e] = n, t)
                for (; t.length;)
                    t[0](e, n), t.splice(0, 1)
        }
    }
    function s(e, n) {
        e.call && (e = { success: e }), n.length ? (e.error || l)(n) : (e.success || l)(e)
    }
    function h(t, r, i, c) {
        var o, s, e = document, n = i.async, f = (i.numRetries || 0) + 1, u = i.before || l, a = t.replace(/^(css|img)!/, "");
        c = c || 0, /(^css!|\.css$)/.test(t) ? (o = !0, (s = e.createElement("link")).rel = "stylesheet", s.href = a) : /(^img!|\.(png|gif|jpg|svg)$)/.test(t) ? (s = e.createElement("img")).src = a : ((s = e.createElement("script")).src = t, s.async = void 0 === n || n), !(s.onload = s.onerror = s.onbeforeload = function (e) {
            var n = e.type[0];
            if (o && "hideFocus" in s)
                try {
                    s.sheet.cssText.length || (n = "e")
                } catch (e) {
                    18 != e.code && (n = "e")
                }
            if ("e" == n && (c += 1) < f)
                return h(t, r, i, c);
            r(t, n, e.defaultPrevented)
        }) !== u(t, s) && e.head.appendChild(s)
    }
    function t(e, n, t) {
        var r, i;
        if (n && n.trim && (r = n), i = (r ? t : n) || {}, r) {
            if (r in c)
                throw "LoadJS";
            c[r] = !0
        }
        !function (e, r, n) {
            var t, i, c = (e = e.push ? e : [e]).length, o = c, s = [];
            for (t = function (e, n, t) { if ("e" == n && s.push(e), "b" == n) { if (!t) return; s.push(e) } --c || r(s) }, i = 0; i < o; i++)
                h(e[i], t, n)
        }(e, function (e) {
            s(i, e), o(r, e)
        }, i)
    }
    return t.ready = function (e, n) {
        return function (e, t) {
            e = e.push ? e : [e];
            var n, r, i, c = [], o = e.length, s = o;
            for (n = function (e, n) { n.length && c.push(e), --s || t(c) }; o--;)
                r = e[o], (i = f[r]) ? n(r, i) : (u[r] = u[r] || []).push(n)
        }(e, function (e) {
            s(n, e)
        }), t
    }, t.done = function (e) {
        o(e, [])
    }, t.reset = function () {
        c = {}, f = {}, u = {}
    }, t.isDefined = function (e) {
        return e in c
    }, t
}();

// =============================================================================

//==============================================================================
// GLOBAL APP MANAGER
//==============================================================================
var APP = {
    // configuracion
    CONFIG: {
        ID_APP: "app",
        ID_DRAWER_SECCIONES: "app-drawer-menu",
        ID_NAV_ACCOUNT: "user-account-info",
        ID_NAV_CENTRAL_APPS: "central-apps-menu",
        USER_BROWSER_CONFIG_KEY: "user_browser_config"
    },

    // Modo Debug
    DEBUG: typeof DEBUG !== "undefined" ? DEBUG : false,

    // Por defecto no mostramos loading screen
    SHOW_LOADER: false,

    // Elementos DOM
    DOM: {},

    ULID: ULID,

    // Pila de estados de vista
    VIEW_STATE_STACK: [],
    state: {},
    parentState: {},
    CONFIG_SOLAPAS: null,

    // Cola de funciones asincronicas
    READY_QUEUE: [],

    isOnline: navigator.onLine,

    ulid: function() {
        return this.ULID.ulid(arguments);
    },

    cargarDOM: function (key, id, hideNotFound = false) {
        // validacion
        if (typeof key !== "string")
            return this.error("key debe ser un string, se recibio:", key);
        if (typeof id !== "string")
            return this.error("id debe ser un string, se recibio:", id);
        if (this.DOM.hasOwnProperty(key))
            return this.error("DOM." + key + " ya se encuentra cargado, no se puede cargar 2 veces");

        // ejecucion
        let element = document.getElementById(id);
        if (!element) {
            element = document.querySelector(id);
        }
        if (element === null) {
            if (!hideNotFound) {
                return this.error("DOM." + key + " no pudo ser cargado porque el elemento de id '" + id + "' no existe");
            } else {
                return false;
            }
        }
        this.DOM[key] = element;
        return true;
    },

    init: function (modulos = {}) {
        // si existe config de usuario la precargamos
        const browserConfig = this.getFromLocal(this.CONFIG.USER_BROWSER_CONFIG_KEY);
        if (browserConfig) {
            this._BROWSER_CONFIG = browserConfig;
        }

        // evento lanzado cuando la libreria haya sido inicializada
        this._initializedEvent = document.createEvent('Event');
        this._initializedEvent.initEvent('app-initialized', false, false);
        this._initialized = false;
        document.addEventListener('app-initialized', this._onInit.bind(this), false);
        this._domLoaded = false;
        document.addEventListener("DOMContentLoaded", this._onDomLoad.bind(this));
        window.addEventListener("load", this._onDomLoad.bind(this));
        this._loaded = false;

        // configurar BASE_URL
        if (typeof BASE_URL === 'undefined') {
            return this.error("BASE_URL undefined");
        }
        this.BASE_URL = BASE_URL;

        // configurar CENTRALASSETS_URL
        if (typeof CENTRALASSETS_URL === 'undefined') {
            return this.error("CENTRALASSETS_URL undefined");
        }
        this.CENTRALASSETS_URL = CENTRALASSETS_URL;
        this.CENTRALMODULES_URL = CENTRALASSETS_URL + '/modulos';

        // BASECSS_LINK -> utilizado para la carga dinamica de modulos
        this.BASECSS_LINK = null;
        for (let i = 0; i < document.head.children.length; i++) {
            const el = document.head.children[i];
            if (el.tagName !== "LINK")
                continue;
            if (el.href && el.href.indexOf("css/base") >= 0) {
                this.BASECSS_LINK = el;
                break;
            }
        }
        if (this.BASECSS_LINK === null) {
            return this.error("Hoja de estilos base no encontrada.");
        }



        // carga de modulos
        this.cargarModulos(modulos);
    },

    _onInit: function () {
        document.removeEventListener('app-initialized', this._onInit);
        this._initialized = true;

        // Load APP
        this._load();
    },

    _onDomLoad: function () {
        if (this._domLoaded)
            return;
        const that = this;
        document.removeEventListener("DOMContentLoaded", this._onDomLoad);
        window.removeEventListener("load", this._onDomLoad);
        this._domLoaded = true;

        if (this.cargarDOM("APP", this.CONFIG.ID_APP, true)) {
            // inicializamos estado de vista principal
            const view = {
                containerElement: this.DOM.APP
            };
            if (typeof TEMPLATE_NAME_BASE !== 'undefined') {
                view.templateName = TEMPLATE_NAME_BASE;
            }
            this.VIEW_STATE_STACK.push(view);

            if (this.isOnline) {
                this.DOM.APP.removeClass("app--offline");
            } else {
                this.DOM.APP.addClass("app--offline");
            }
        }

        this.cargarDOM("APP_TITLE", ".app-brand .app-title", true);
        this.cargarDOM("APP_SOLAPAS", "#app-solapas-container", true);
        this.cargarDOM("APP_SOLAPAS_2", "#app-solapas-container-2", true);

        // construir e insertar loader
        if (this.cargarDOM("LOADING_SCREEN", ".app-loading-screen", true)) {
            const loader = document.createElement('div');
            loader.className = 'app-loader-ring';
            loader.innerHTML = '<div></div><div></div><div></div><div></div>';
            if (!this.SHOW_LOADER) {
                this.endLoading(); // by default stop loader fast
                window.setTimeout(function () {
                    that.DOM.LOADING_SCREEN.appendChild(loader);
                }, 500);
            } else {
                this.DOM.LOADING_SCREEN.appendChild(loader);
            }
        }

        // DRAWER -> desplegables laterales
        this.cargarDOM("DRAWER_SECCIONES", this.CONFIG.ID_DRAWER_SECCIONES, true);

        // NAV -> desplegables desde cabecera
        this.cargarDOM("NAV_ACCOUNT", this.CONFIG.ID_NAV_ACCOUNT, true);
        this.cargarDOM("NAV_CENTRAL_APPS", this.CONFIG.ID_NAV_CENTRAL_APPS, true);

        // APLICAR BROWSER CONFIG
        this.configBrowser();

        // Load APP
        this._load();
    },

    _load: function () {
        if (!this._domLoaded || !this._initialized)
            return;
        const that = this;
        const modulos = Object.keys(that.MODULOS.utilizados);
        if (modulos) {
            const req = modulos.map(function (m) {
                if (that.MODULOS.utilizados[m].archivos.indexOf("modulo.js") >= 0) {
                    return m + "_MODULO";
                } else {
                    return m;
                }
            });
            loadjs.ready(req, () => {
                this._onLoad();
            });
        } else {
            this._onLoad();
        }
    },

    _onLoad: function () {
        this._loaded = true;

        // app is focused??
        this.wasFocused = true;
        this.isFocused = true;
        if (this.DOM.APP) {
            window.addEventListener('focus', this.onFocusChange.bind(this), true);
            window.addEventListener('blur', this.onFocusChange.bind(this), true);
        }
        this.endLoading();

        // app is online?
        window.addEventListener('online', this.onNetworkStatusChange.bind(this), true);
        window.addEventListener('offline', this.onNetworkStatusChange.bind(this), true);

        // Run
        this._onReady();
    },

    _onReady: function () {
        const f = this.READY_QUEUE.pop();
        if (f) {
            f();
            this._onReady();
        }
    },

    ready: function (callback) {
        if (typeof callback !== "function")
            return this.error("callback debe ser una funcion, se recibio:", callback);

        this.READY_QUEUE.unshift(callback);

        if (this._loaded) {
            window.setTimeout(this._onReady.bind(this));
        }
    },

    showLoader: function () {
        this.SHOW_LOADER = true;
    },

    startLoading: function () {
        if (!this.DOM.LOADING_SCREEN)
            return;
        this.DOM.LOADING_SCREEN.addClass("loading");
    },
    endLoading: function () {
        if (!this.DOM.LOADING_SCREEN)
            return;
        this.DOM.LOADING_SCREEN.removeClass("loading");
    },

    startProcessing: function () {
        if (!this.DOM.APP)
            return;
        this.DOM.APP.addClass("app--processing");
    },
    endProcessing: function () {
        if (!this.DOM.APP)
            return;
        this.DOM.APP.removeClass("app--processing");
    },

    // LOGGING
    debug: function (...params) {
        if (this.DEBUG)
            console.debug("%c DEBUG\t", "color: #03A9F4; font-weight: bold", ...params);
    },
    info: function (...params) {
        console.info("%c INFO\t", "color: #4CAF50; font-weight: bold", ...params);
    },
    warn: function (...params) {
        console.warn("%c WARN\t", "color: #F39C12; font-weight: bold", ...params);
    },
    error: function (...params) {
        console.error("%c ERROR\t", "color: #F20404; font-weight: bold", ...params);
        return false;
    },

    // ON FOCUS CHANGE QUEUE
    _onFocus: [],
    registerOnFocus: function (id, fn) {
        this._onFocus[id] = fn;
    },
    unregisterOnFocus: function (id) {
        delete this._onFocus[id];
    },

    // ON FOCUS CHANGE
    onFocusChange: function (event) {
        this.isFocused = document.hasFocus();
        if (this.isFocused !== this.wasFocused) {
            if (!this.isFocused) {
                this.DOM.APP.addClass("app--not-focused");
            } else {
                this.DOM.APP.removeClass("app--not-focused");
                for (var key in this._onFocus) {
                    this._onFocus[key]();
                }
            }
            this.wasFocused = this.isFocused;
        }
    },

    // ON NETWORK STATUS CHANGE QUEUE
    _onNetworkStatus: [],
    registerOnNetworkStatus: function (id, fn) {
        this._onNetworkStatus[id] = fn;
    },
    unregisterOnNetworkStatus: function (id) {
        delete this._onNetworkStatus[id];
    },

    // ON NETWORK STATUS CHANGE
    onNetworkStatusChange: function (event) {
        this.isOnline = navigator.onLine;

        if (this.DOM && this.DOM.APP) {
            if (this.isOnline) {
                this.DOM.APP.removeClass("app--offline");
            } else {
                this.DOM.APP.addClass("app--offline");
            }
        }

        for (let key in this._onNetworkStatus) {
            this._onNetworkStatus[key](this.isOnline);
        }
    },

    // VIEW STATE UTILITIES
    currentViewState: function () {
        return this.VIEW_STATE_STACK[this.VIEW_STATE_STACK.length - 1];
    },

    setearTemplateName: function (templateName) {
        let view = this.currentViewState();
        view.templateName = templateName;
    },

    getTemplateName: function () {
        let view = this.currentViewState();
        if (!view.templateName) {
            APP.info('templateName indefinido');
            return;
        }
        APP.info(view.templateName);
    },

    setearAppTitle: function (title) {
        if (!this.DOM.APP_TITLE) return;

        let view = this.currentViewState();
        if (!view.appTitleAnterior) {
            view.appTitleAnterior = this.DOM.APP_TITLE.textContent;
        }
        this.DOM.APP_TITLE.textContent = title;
    },

    setearAppSolapasModoEdicion: function () {
        if (!this.DOM.APP_SOLAPAS) return;

        let view = this.currentViewState();
        if (!view.appSolapasModoEdicionAnterior && this.DOM.APP_SOLAPAS.hasClass('modo_edicion')) {
            view.appSolapasModoEdicionAnterior = true;
        }

        this.DOM.APP.addClass('solapas--edicion');
        if (this.DOM.APP_SOLAPAS) this.DOM.APP_SOLAPAS.addClass('modo_edicion');
        if (this.DOM.APP_SOLAPAS_2) this.DOM.APP_SOLAPAS_2.addClass('modo_edicion');
    },

    registrarSolapas: function (config) {
        if (this.CONFIG_SOLAPAS) return;

        this.CONFIG_SOLAPAS = {
            _activoStack: []
        };

        for (let key in config) {
            if (config.hasOwnProperty(key)) {
                const c = config[key];
                if (!c.url && !c.inicial) {
                    APP.error('la configuracion de la solapa debe poseer una url o ser el valor inicial', config);
                    continue;
                }
                this.CONFIG_SOLAPAS[key] = c;
                if (c.inicial) {
                    this.CONFIG_SOLAPAS._activoStack.push(key);
                    this.CONFIG_SOLAPAS._initialView = APP.currentViewState().containerElement;
                    this.CONFIG_SOLAPAS._initialState = APP.state;
                    this.CONFIG_SOLAPAS._initialParentState = APP.parentState;
                }
            }
        }
        this.currentViewState().solapaConfig = this.CONFIG_SOLAPAS;
    },

    setearAppSolapas: function (solapas) {
        if (!this.DOM.APP_SOLAPAS) return;

        let view = this.currentViewState();
        if (view.keepSolapas) return;
        if (!view.appSolapasAnterior) {
            view.appSolapasAnterior = this.DOM.APP_SOLAPAS.innerHTML;
            if (this.DOM.APP_SOLAPAS_2) {
                view.appSolapasAnterior2 = this.DOM.APP_SOLAPAS_2.innerHTML;
            }
        }

        if (!solapas) {
            this.DOM.APP_SOLAPAS.innerHTML = "";
            if (this.DOM.APP_SOLAPAS_2) {
                this.DOM.APP_SOLAPAS_2.innerHTML = "";
            }
            this.DOM.APP.removeClass('solapas--edicion');
            if (this.DOM.APP_SOLAPAS) {
                this.DOM.APP_SOLAPAS.removeClass('modo_edicion');
            }
            if (this.DOM.APP_SOLAPAS_2) {
                this.DOM.APP_SOLAPAS_2.removeClass('modo_edicion');
            }
            this.DOM.APP.removeClass('app--with-solapas');
            return;
        }

        let html = '';
        let idSuffix = '';
        const generador = (solapa, solapaId) => {
            if (solapa.dropdown) {
                html += `<div class="app__solapa dropdown">
                            <button class="btn dropdown-toggle" type="button" id="appSolapaSelect${idSuffix}_${solapaId}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                ${solapa.nombre}
                            </button>
                            <div class="dropdown-menu" aria-labelledby="appSolapaSelect${idSuffix}_${solapaId}">`

                solapa.options.forEach(option => {
                    html += `<button type="button" class="dropdown-item" onclick="APP.solapaOnClick(this, '${solapa.onClick}');" data-parentid="appSolapaSelect${idSuffix}_${solapaId}"  data-id="${option.id}">${option.value}</button>`;
                });

                html += `</div>
                        </div>`;
            } else {
                html += `<a class="app__solapa `;
                if (solapa.activo) {
                    html += 'app__solapa--activa';
                }
                html += '"';
                if (solapa.url) {
                    html += ` href="${solapa.url}" `;
                } else {
                    html += ` onclick="APP.solapaOnClick(this, '${solapa.onClick}');" data-id="${solapa.id}" `;
                }
                html += `>${solapa.nombre}</a>`;
            }
        };

        solapas.forEach(generador);
        this.DOM.APP_SOLAPAS.innerHTML = html;

        if (this.DOM.APP_SOLAPAS_2) {
            html = '';
            idSuffix = '2';
            solapas.forEach(generador);
            this.DOM.APP_SOLAPAS_2.innerHTML = html;
        }
        this.DOM.APP.addClass('app--with-solapas');
    },

    currentAppState: function () {
        let view = this.currentViewState();
        return view.appState;
    },

    setearAppState: function (state) {
        if (!this.DOM.APP) return;

        let view = this.currentViewState();
        view.appState = state;
        this.DOM.APP.addClass('state--' + state);
    },

    solapaOnClick: function (el, onClick) {
        if (el.dataset.parentid) {
            // dropdown
            const parent = document.getElementById(el.dataset.parentid);
            if (parent) {
                parent.innerText = el.innerText;
            }
        } else {
            // solapa normal
            if (this.DOM.APP_SOLAPAS) {
                for (let i = 0; i < this.DOM.APP_SOLAPAS.children.length; i++) {
                    let solapaElement = this.DOM.APP_SOLAPAS.children[i];
                    solapaElement.removeClass('app__solapa--activa');
                }
            }
            if (this.DOM.APP_SOLAPAS_2) {
                for (let i = 0; i < this.DOM.APP_SOLAPAS_2.children.length; i++) {
                    let solapaElement = this.DOM.APP_SOLAPAS_2.children[i];
                    solapaElement.removeClass('app__solapa--activa');
                }
            }

            el.addClass('app__solapa--activa');
        }

        APP.executeFunctionByName(onClick, window, el.dataset.id, el.innerText);
    },

    executeFunctionByName: function (functionName, context = window /*, args */) {
        var args = Array.prototype.slice.call(arguments, 2);
        var namespaces = functionName.split(".");
        var func = namespaces.pop();
        for (var i = 0; i < namespaces.length; i++) {
            context = context[namespaces[i]];
        }
        if (typeof context[func] !== "function") {
            APP.error('nombre de funcion inexistente:', functionName);
            return;
        }
        return context[func].apply(context, args);
    },

    updateParent: function () {
        this.parentState._update = true;

        // clean caches
        for (let key in this.CONFIG_SOLAPAS) {
            if (!this.CONFIG_SOLAPAS.hasOwnProperty(key)) continue;
            const config = this.CONFIG_SOLAPAS[key];
            if (!config.hasOwnProperty("_loaded")) continue;
            delete config._loaded;
            delete config._state;
        }
    },

    openView: function (element, options = {}) {
        let onClose = options.hasOwnProperty("onClose") ? options.onClose : null;
        let keepHeader = options.hasOwnProperty("keepHeader") ? options.keepHeader : false;
        let closeWithOverlay = options.hasOwnProperty("closeWithOverlay") ? options.closeWithOverlay : false;
        let isSolapa = options.hasOwnProperty("isSolapa") ? options.isSolapa : false;
        let keepSolapa = options.hasOwnProperty("keepSolapa") ? options.keepSolapa : true;

        let desdePortada = this.DOM.APP.hasClass('app--portada');

        if (keepHeader) {
            this.DOM.APP.addClass('app--keep-header');
            this.DOM.APP.removeClass('app--portada');
        } else {
            this.DOM.APP.removeClass('app--keep-header');
        }
        this.DOM.APP.removeClass('app--from-keep-header');

        let view = this.currentViewState();
        if (view.appState && !keepSolapa) {
            this.DOM.APP.removeClass('state--' + view.appState);
        }
        if (!keepSolapa) {
            APP.CONFIG_SOLAPAS = null;
        }
        view.containerElement.removeClass("active-view");
        view.state = this.state;
        view.parentState = this.parentState;

        this.state = {};
        this.parentState = view.state;
        if (isSolapa) {
            this.parentState = APP.CONFIG_SOLAPAS._initialState;
        }

        this.VIEW_STATE_STACK.push({
            containerElement: element,
            onClose: onClose,
            keepHeader: keepHeader,
            closeWithOverlay: closeWithOverlay,
            keepSolapa: keepSolapa,
            isSolapa: isSolapa,
            solapaConfig: keepSolapa ? APP.CONFIG_SOLAPAS : null,
            appState: keepSolapa ? view.appState : null,
            appSolapasModoEdicionAnterior: isSolapa && view.appSolapasModoEdicionAnterior,
            desdePortada: desdePortada
        });
        element.addClass("active-view");
    },

    onCloseView: function(fn) {
        const view = this.currentViewState();
        if (!view.ON_CLOSE_VIEW_CALLBACKS) {
            view.ON_CLOSE_VIEW_CALLBACKS = [];
        }
        view.ON_CLOSE_VIEW_CALLBACKS.push(fn);
    },

    closeCurrentView: function (cerrarSoloSolapas = false) {
        if (this.VIEW_STATE_STACK.length <= 1)
            return null;
        if (cerrarSoloSolapas && !this.currentViewState().isSolapa)
            return null;

        const viewState = this.VIEW_STATE_STACK.pop();
        if (viewState.appTitleAnterior) {
            this.DOM.APP_TITLE.textContent = viewState.appTitleAnterior;
        }
        if (viewState.appSolapasAnterior) {
            if (this.DOM.APP_SOLAPAS) {
                this.DOM.APP_SOLAPAS.innerHTML = viewState.appSolapasAnterior;
            }
            if (this.DOM.APP_SOLAPAS_2) {
                this.DOM.APP_SOLAPAS_2.innerHTML = viewState.appSolapasAnterior2;
            }
            if (viewState.appSolapasAnterior.trim() === "") {
                this.DOM.APP.removeClass('app--with-solapas');
            } else {
                this.DOM.APP.addClass('app--with-solapas');
            }
        }
        if (viewState.appSolapasModoEdicionAnterior) {
            this.DOM.APP.addClass('solapas--edicion');
            if (this.DOM.APP_SOLAPAS) {
                this.DOM.APP_SOLAPAS.addClass('modo_edicion');
            }
            if (this.DOM.APP_SOLAPAS_2) {
                this.DOM.APP_SOLAPAS_2.addClass('modo_edicion');
            }
        } else if (!viewState.keepSolapa) {
            this.DOM.APP.removeClass('solapas--edicion');
            if (this.DOM.APP_SOLAPAS) {
                this.DOM.APP_SOLAPAS.removeClass('modo_edicion');
            }
            if (this.DOM.APP_SOLAPAS_2) {
                this.DOM.APP_SOLAPAS_2.removeClass('modo_edicion');
            }
        }
        if (viewState.appState) {
            this.DOM.APP.removeClass('state--' + viewState.appState);
        }
        if (viewState.isSolapa) {
            APP.CONFIG_SOLAPAS._activoStack.pop();
        }
        viewState.containerElement.removeClass("active-view");
        if (viewState.ON_CLOSE_VIEW_CALLBACKS) {
            viewState.ON_CLOSE_VIEW_CALLBACKS.forEach(fn => fn());
            viewState.ON_CLOSE_VIEW_CALLBACKS.length = 0;
        }
        if (viewState.onClose !== null) {
            viewState.onClose(viewState);
        }
        if (viewState.desdePortada) {
            this.DOM.APP.addClass('app--portada');
        }
        if (this.VIEW_STATE_STACK.length > 1 && this.currentViewState().keepHeader) {
            this.DOM.APP.addClass('app--keep-header');
        } else {
            if (this.DOM.APP.hasClass('app--keep-header')) {
                this.DOM.APP.removeClass('app--keep-header');
                this.DOM.APP.addClass("app--from-keep-header");
            }
        }

        let view = this.currentViewState();
        view.containerElement.addClass("active-view");
        this.state = view.state;
        this.parentState = view.parentState;
        if (this.state._update) {
            this.state._update = false;
            if (typeof this.state.onUpdate === "function") this.state.onUpdate();
        }

        if (view.appState) {
            this.DOM.APP.addClass('state--' + view.appState);
        }
        if (view.solapaConfig) {
            APP.CONFIG_SOLAPAS = view.solapaConfig;
        } else {
            APP.CONFIG_SOLAPAS = null;
        }

        if (viewState.isSolapa) {
            // si la vista que habia era una solapa continuamos cerrando las vistas
            return this.closeCurrentView(cerrarSoloSolapas);
        }
        return viewState;
    },

    // CARGADOR DE MODULOS JS
    cargarModulos: function (modulos = {}) {
        const that = this;
        let dataUrl = this.CENTRALMODULES_URL + '/data.json';
        if (typeof APP_VERSION !== "undefined") {
            dataUrl += "?" + APP_VERSION;
        }
        const xhr = new XMLHttpRequest();
        xhr.open('GET', dataUrl, true);
        xhr.onload = function () {
            if (xhr.status >= 400) {
                that.error("no se pudo leer", dataUrl);
                return;
            }
            // cargamos data de modulos
            that.MODULOS = {
                utilizados: {},
                data: JSON.parse(xhr.responseText)
            };

            // analisis de dependencias
            let modulosID = Object.keys(modulos);
            modulosID.forEach(id => {
                let version = modulos[id];
                if (typeof version !== "string") {

                    const versiones = that.MODULOS.data[id];
                    version = Object.keys(versiones).sort()[0];
                    that.debug("modulo", id, "version sin definir, se cargara version", version);
                }

                that._prepararModulo(id, version);
            });

            // carga de archivos
            modulosID = Object.keys(that.MODULOS.utilizados);
            modulosID.forEach(id => {
                const modulo = that.MODULOS.utilizados[id];
                const dependencias = Object.keys(modulo.dependencias);
                if (dependencias.length > 0) {
                    loadjs.ready(dependencias, () => {
                        that._cargarModulo(id, modulo);
                    });
                } else {
                    that._cargarModulo(id, modulo);
                }
            });
            document.dispatchEvent(that._initializedEvent);
        };
        xhr.send();
    },

    _prepararModulo: function (id, version, requeridoPor = null) {
        // obtener versiones disponibles
        if (!this.MODULOS.data.hasOwnProperty(id)) {
            return this.error("modulo", id, "no existe");
        }
        const versiones = this.MODULOS.data[id];

        // obtener version especifica
        if (!versiones.hasOwnProperty(version)) {
            return this.error("modulo", id, "version", version, "no existe");
        }

        // encolar para carga
        if (this.MODULOS.utilizados.hasOwnProperty(id)) {
            // validar que concuerden las versiones
            if (this.MODULOS.utilizados[id].version !== version) {
                return this.error("modulo", id, version, "conflicto de versiones", this.MODULOS.utilizados[id]);
            }

            if (requeridoPor !== null) {
                this.MODULOS.utilizados[id].requeridoPor.push(requeridoPor);
            }
        } else {
            const dependencias = versiones[version].dependencias || {};
            this.MODULOS.utilizados[id] = {
                version: version,
                archivos: versiones[version].archivos,
                dependencias: dependencias,
                requeridoPor: []
            };
            if (requeridoPor !== null) {
                this.MODULOS.utilizados[id].requeridoPor.push(requeridoPor);
            }
            const that = this;
            Object.keys(dependencias).forEach(dependenciaID => {
                that._prepararModulo(dependenciaID, dependencias[dependenciaID], id);
            });
        }
        return true;
    },

    _cargarModulo: function (id, modulo) {
        const that = this;
        if (!loadjs.isDefined(id)) {
            let cacheVersion = '?v=';
            if (typeof APP_VERSION !== "undefined") {
                cacheVersion += APP_VERSION;
            }
            loadjs(modulo.archivos.map(archivo => {
                const ending = cacheVersion + "." + archivo.split(".").pop();
                return that.CENTRALMODULES_URL + "/" + id.toLowerCase() + "/" + modulo.version + "/" + archivo + ending
            }), id, {
                success: function () {
                    //that.debug("modulo", id, modulo.version, "cargado");
                },
                error: function (pathsNotFound) {
                    that.debug("modulo", id, modulo.version, "no cargado, no se encontraron los siguientes archivos", pathsNotFound);
                },
                before: function (path, scriptEl) {
                    // solo modificamos la carga de css
                    if (!path.endsWith("css"))
                        return true;
                    if (path.endsWith("modulo.css")) {
                        // cargamos despues de base
                        scriptEl.appendAfter(that.BASECSS_LINK);
                    } else {
                        // cargamos antes de base
                        scriptEl.appendBefore(that.BASECSS_LINK);
                    }
                    return false;
                }
            });
        }
    },

    BACK_TO: null,
    setearBackTo: function (url) {
        this.BACK_TO = url;
    },

    saveToLocal: function (id, value) {
        window.localStorage.setItem(id, JSON.stringify(value));
    },

    getFromLocal: function (id) {
        return JSON.parse(window.localStorage.getItem(id));
    },

    _BROWSER_CONFIG: {
        darkMode: false
    },
    configBrowser: function() {
        if (this._BROWSER_CONFIG.darkMode) {
            // dark mode
            document.body.removeClass("light-mode");
            document.body.addClass("dark-mode");
        } else {
            // light mode
            document.body.removeClass("dark-mode");
            document.body.addClass("light-mode");
        }
    },
    getBrowserConfig: function(key=null) {
        if (key) {
            return this._BROWSER_CONFIG[key];
        }
        return Object.assign({}, this._BROWSER_CONFIG);
    },
    setBrowserConfig: function(key, value) {
        this._BROWSER_CONFIG[key] = value;
        this.saveToLocal(this.CONFIG.USER_BROWSER_CONFIG_KEY, this._BROWSER_CONFIG);
        this.configBrowser();
    },
    darkMode: function() {
        this.setBrowserConfig("darkMode", true);
    },
    lightMode: function() {
        this.setBrowserConfig("darkMode", false);
    },
    toggleDarkMode: function() {
        const darkMode = this.getBrowserConfig("darkMode");
        this.setBrowserConfig("darkMode", !darkMode);
    }
};


//==============================================================================
// APP SHELL
//==============================================================================
if (typeof volver === 'undefined') {
    window.volver = function (baseUrl = null) {
        if (APP.VIEW_STATE_STACK.length > 1) {
            APP.closeCurrentView();
            return;
        }
        if (APP.BACK_TO) {
            window.location.replace(APP.BACK_TO);
            return;
        }
        if (baseUrl !== null) {
            window.location.replace(baseUrl);
            return;
        }
        window.location.replace(APP.BASE_URL);
    };
}

function openDrawerMenu() {
    APP.openView(APP.DOM.DRAWER_SECCIONES, {
        closeWithOverlay: true
    });
}

function closeDrawer() {
    if (APP.currentViewState().closeWithOverlay)
        APP.closeCurrentView();
}

function closeAccountInfo(e) {
    if (!APP.DOM.NAV_ACCOUNT.is(e.target) && !APP.DOM.NAV_ACCOUNT.contains(e.target)) {
        document.removeEventListener("click", closeAccountInfo);
        APP.DOM.NAV_ACCOUNT.removeClass("visible");
    }
}

function openAccountInfo() {
    if (APP.DOM.NAV_ACCOUNT.hasClass("visible"))
        return;
    APP.DOM.NAV_ACCOUNT.addClass("visible");
    setTimeout(function () {
        document.addEventListener("click", closeAccountInfo);
    }, 0);
}

function closeCentralAppsMenu(e) {
    if (!APP.DOM.NAV_CENTRAL_APPS.is(e.target) && !APP.DOM.NAV_CENTRAL_APPS.contains(e.target)) {
        document.removeEventListener("click", closeCentralAppsMenu);
        APP.DOM.NAV_CENTRAL_APPS.removeClass("visible");
    }
}

function openCentralAppsMenu() {
    if (APP.DOM.NAV_CENTRAL_APPS.hasClass("visible"))
        return;
    APP.DOM.NAV_CENTRAL_APPS.addClass("visible");
    setTimeout(function () {
        document.addEventListener("click", closeCentralAppsMenu);
    }, 0);
}

function openBodyDrawer(selector) {
    let drawer = document.getElementById(selector);
    if (!drawer) drawer = document.querySelector(selector);

    APP.openView(drawer, {
        keepHeader: true,
        closeWithOverlay: true
    });
}
//==============================================================================
// UTILITIES
//==============================================================================

function urlWithGetData(url, data) {
    return url + "?" + encodeGetData(data);
}

function encodeGetData(data) {
    return Object.keys(data).map(function (key) {
        return [key, data[key]].map(encodeURIComponent).join("=");
    }).join("&");
}

function base64_encode(data) {
    var b64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
    var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
        ac = 0,
        enc = '',
        tmp_arr = [];

    if (!data) {
        return data;
    }

    do { // pack three octets into four hexets
        o1 = data.charCodeAt(i++);
        o2 = data.charCodeAt(i++);
        o3 = data.charCodeAt(i++);
        bits = o1 << 16 | o2 << 8 | o3;
        h1 = bits >> 18 & 0x3f;
        h2 = bits >> 12 & 0x3f;
        h3 = bits >> 6 & 0x3f;
        h4 = bits & 0x3f;
        // use hexets to index into b64, and append result to encoded string
        tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
    } while (i < data.length);
    enc = tmp_arr.join('');
    var r = data.length % 3;
    return (r ? enc.slice(0, r - 3) : enc) + '==='.slice(r || 3);
}

function decode64(input) {
    var output = "";
    var chr1, chr2, chr3 = "";
    var enc1, enc2, enc3, enc4 = "";
    var i = 0;

    // remove all characters that are not A-Z, a-z, 0-9, +, /, or =
    var base64test = /[^A-Za-z0-9\+\/\=]/g;
    if (base64test.exec(input)) {
        alert("There were invalid base64 characters in the input text.\n" +
            "Valid base64 characters are A-Z, a-z, 0-9, '+', '/',and '='\n" +
            "Expect errors in decoding.");
    }
    input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

    do {
        enc1 = keyStr.indexOf(input.charAt(i++));
        enc2 = keyStr.indexOf(input.charAt(i++));
        enc3 = keyStr.indexOf(input.charAt(i++));
        enc4 = keyStr.indexOf(input.charAt(i++));

        chr1 = (enc1 << 2) | (enc2 >> 4);
        chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
        chr3 = ((enc3 & 3) << 6) | enc4;

        output = output + String.fromCharCode(chr1);

        if (enc3 != 64) {
            output = output + String.fromCharCode(chr2);
        }
        if (enc4 != 64) {
            output = output + String.fromCharCode(chr3);
        }

        chr1 = chr2 = chr3 = "";
        enc1 = enc2 = enc3 = enc4 = "";

    } while (i < input.length);

    return unescape(output);
}

function caracteres_especiales(campo) {
    var ltr = ['[�����]', '[����]', '[����]', '[�����]', '[����]', '�', '�', '[��]', '[\[(){}*+?^$!"#$%&()?=_,:;��"\'&@.<>|]', '[��]', '/'];
    var rpl = ['a', 'e', 'i', 'o', 'u', 'n', 'c', 'y', '', '', '/'];

    for (var i = 0, c = ltr.length, r = String(campo.toLowerCase()); i < c; i++) {
        var rg = new RegExp(ltr[i], 'g');
        r = r.replace(rg, rpl[i]);
    }
    return r;
}

function trim(value) {
    return value.replace(/^\s+/, "").replace(/\s+$/, "");
}

function isNumber(value) {
    var regNumber = /[\d]/;
    return regNumber.test(value);
}

function isLetter(value) {
    var regNumber = /^[a-zA-Z �����A������\s]/;
    return regNumber.test(value);
}

function isAlfaNumeric(value) {
    var regNumber = /[A-Za-z��0-9\s]/;
    return regNumber.test(value);
}

function isEmail(value) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(value);
}

function isDate(value) {
    var regNumber = /^((([0][1-9]|[12][\d])|[3][01])[-\/]([0][13578]|[1][02])[-\/][1-9]\d\d\d)|((([0][1-9]|[12][\d])|[3][0])[-\/]([0][13456789]|[1][012])[-\/][1-9]\d\d\d)|(([0][1-9]|[12][\d])[-\/][0][2][-\/][1-9]\d([02468][048]|[13579][26]))|(([0][1-9]|[12][0-8])[-\/][0][2][-\/][1-9]\d\d\d)$/;
    return regNumber.test(value);
}

function validateNumber(e) {
    var evt = e.keyCode ? e.keyCode : e.which;
    if (evt == 13 || evt == 8 || evt == 9) {
        return true;
    }
    return isNumber(String.fromCharCode(evt));
}

function validateLetter(e) {
    var evt = e.keyCode ? e.keyCode : e.which;
    if (evt == 13 || evt == 8) {
        return true;
    }
    return isLetter(String.fromCharCode(evt));
}

function validateAlfaNumeric(e) {
    var evt = e.keyCode ? e.keyCode : e.which;
    if (evt == 13 || evt == 8) {
        return true;
    }
    return isAlfaNumeric(String.fromCharCode(evt));
}

function round(number, length) {
    return Math.round(number * Math.pow(10, length)) / Math.pow(10, length);
}

function BlockChars(event) {
    var keycode = event.keyCode;

    var valid =
        (keycode > 47 && keycode < 58) || // number keys
        (keycode === 32 || keycode === 13) || // spacebar & return key(s) (if you want to allow carriage returns)
        (keycode > 64 && keycode < 91) || // letter keys
        (keycode > 95 && keycode < 112) || // numpad keys
        (keycode > 185 && keycode < 193) || // ;=,-./` (in order)
        (keycode > 218 && keycode < 223);   // [\]' (in order)

    return valid;
}