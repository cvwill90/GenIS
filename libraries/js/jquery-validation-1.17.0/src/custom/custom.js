/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$.validator.addMethod('valid_option', function(value, element) {
    return this.optional(element) || parseInt(value) !== 0;
}, "Veuillez choisir une option valide.");

$.validator.addMethod('valid_maxdate', function(value, element, param) {
    return this.optional(element) || value >= param;
}, "Veuillez choisir une date finale supérieure à la date de départ");

$.validator.addMethod('valid_farm', function(value, element, param) {
    return this.optional(element) || param !== 'undef';
}, "Veuillez choisir une ferme valide");