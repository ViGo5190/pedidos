/**
 * Created by vigo5190
 */

function PedidosCreateForm() {
    this.form = $("#createForm");
}

PedidosCreateForm.prototype.click = function (event) {
    console.log(event);
};

PedidosCreateForm.prototype.isNumeric = function (obj) {
    return !jQuery.isArray(obj) && (obj - parseFloat(obj) + 1) >= 0;
};

PedidosCreateForm.prototype.init = function () {
    var self = this;
    this.form.submit(function (event) {

        var cost = self.form.find("#cost").val();
        var name = self.form.find("#name").val();
        var desc = self.form.find("#desc").val();


        self.form.find("#div-cost").removeClass('has-error');
        var e = self.form.find("#div-cost").find("#div-cost-error");
        e.empty();
        e.addClass('hidden');
        if (!self.isNumeric(cost)) {
            self.form.find("#div-cost").addClass('has-error').hide().fadeIn("slow");
            var e = self.form.find("#div-cost").find("#div-cost-error");
            e.append("Некорректная сумма!").hide().fadeIn("slow");
            e.removeClass('hidden');
            event.preventDefault();
            return false;
        }

        if (cost <= 0) {
            self.form.find("#div-cost").addClass('has-error').hide().fadeIn("slow");
            var e = self.form.find("#div-cost").find("#div-cost-error");
            e.append("Слишком маленькая сумма!").hide().fadeIn("slow");
            e.removeClass('hidden');
            event.preventDefault();
            return false;
        }

        $.ajax({
            dataType: "json",
            url: '/api.php',
            context: this,
            async: false,
            data: {action: 'createOrder', name: name, desc: desc, cost: cost},
            success: function (data) {
                if (data.status == 6) {
                    self.form.find("#div-cost").addClass('has-error').hide().fadeIn("slow");
                    var e = self.form.find("#div-cost").find("#div-cost-error");
                    e.append("Не хватает денег!").hide().fadeIn("slow");
                    e.removeClass('hidden');
                    event.preventDefault();
                    return false;
                } else if (data.status == 1) {
                    if (data.data.orderId > 0) {
                        addInfo('success', ' Заказ создан. Его номер: ' + data.data.orderId + '.');
                        self.form.find("#cost").val("");
                        self.form.find("#name").val("");
                        self.form.find("#desc").val("");
                    }
                } else {
                    addInfo('error', ' Критическая ошибка! невозможно создать заказ.');
                }
            },
            error: handleFailedAjax
        });


        event.preventDefault();
        return false;
    });
};

function PedidosOrderListForAuthor() {
    this.orders = [];
    this.renderedOrdersIDs = [];
}

PedidosOrderListForAuthor.prototype.init = function (orders) {
    this.orders = orders;
};

PedidosOrderListForAuthor.prototype.renderOrders = function () {
    var self = this;
    console.log('fff');
    renderedOrdersIds = [];
    $.each(this.orders, function (index, value) {
        console.log(self.renderedOrdersIDs);
        if ($.inArray(value.id, self.renderedOrdersIDs) == -1) {
            console.log(value.id);
            renderedOrdersIds.push(value.id);
            self.renderedOrdersIDs.push(value.id);

            var statusDefault="alert-danger";
            var statusDefaultText="Ошибка";


            if (value.status == 1){
                statusDefault = "alert-warning";
                statusDefaultText = " Новый";
            } else if (value.status == 3){
                statusDefault = "alert-info";
                statusDefaultText = " Готов к выполнению";
            } else if(value.status == 5){
                statusDefault = "alert-success";
                statusDefaultText = " Выполнен";
            }

            var orderTitle = $("<h3>" + value.name + "</h3>");
            var orderDesc = $("<p>" + value.describe + "</p>");
            var orderCost = $("<p> Стоимость:  $" + value.cost + "</p>");
            var orderStatus = $("<p class=\""+statusDefault+"\"> Статус: " + statusDefaultText + "</p>");


            var orderCaption = $("<div class=\"caption\"></div>");
            var orderThumbnail = $("<div class=\"thumbnail\"></div>")
            var orderDev = $("<div class=\"col-md-3 col-sm-6 hero-feature\"></div>")

            orderCaption.append(orderTitle);
            orderCaption.append(orderDesc);
            orderCaption.append(orderCost);
            orderCaption.append(orderStatus);

            orderThumbnail.append(orderCaption);
            orderDev.append(orderThumbnail);

            orderDev.prependTo('#orders').hide().fadeIn("slow");

        }
    });

};


function PedidosOrderListExecutor() {
    this.orders = [];
    this.renderedOrdersIDs = [];
}

PedidosOrderListExecutor.prototype.init = function (orders) {
    this.orders = orders;
};

PedidosOrderListExecutor.prototype.renderOrders = function () {
    var self = this;
    console.log('fff');
    renderedOrdersIds = [];
    $.each(this.orders, function (index, value) {
        console.log(self.renderedOrdersIDs);
        if ($.inArray(value.id, self.renderedOrdersIDs) == -1) {
            console.log(value.id);
            renderedOrdersIds.push(value.id);
            self.renderedOrdersIDs.push(value.id);

            var orderTitle = $("<h3>" + value.name + "</h3>");
            var orderDesc = $("<p>" + value.describe + "</p>");
            var orderCost = $("<p>" + value.cost + "</p>");


            var orderCaption = $("<div class=\"caption\"></div>");
            var orderThumbnail = $("<div class=\"thumbnail\"></div>")
            var orderDev = $("<div class=\"col-md-3 col-sm-6 hero-feature\"></div>")

            orderCaption.append(orderTitle);
            orderCaption.append(orderDesc);
            orderCaption.append(orderCost);

            orderThumbnail.append(orderCaption);
            orderDev.append(orderThumbnail);

            orderDev.prependTo('#orders').hide().fadeIn("slow");

        }
    });

};


function PedidosObject() {
    this.orders = [];
    this.userInfo = [];
    this.orderList = {};
    this.createFrom = {};
}

PedidosObject.prototype.updateBalance = function () {
    $.ajax({
        dataType: "json",
        url: '/api.php',
        data: {action: 'getBalance'},
        success: function (data) {
            if (data) {
                $('#pedidos-balance').html(data.data.balance)
            }
        },
        error: handleFailedAjax
    });
};
PedidosObject.prototype.init = function () {
    self = this;
    $.ajax({
        dataType: "json",
        url: '/api.php',
        context: this,
        async: false,
        data: {action: 'getUserInfo'},
        success: function (data) {
            //$('#loading').hide();
            if (data.data) {
                this.userInfo = data.data;
                console.log(this.userInfo);
            }
        },
        error: handleFailedAjax
    });
    if (this.userInfo.type == 1) {
        this.orderList = new PedidosOrderListForAuthor();

    } else if (this.userInfo.type == 2) {
        this.orderList = new PedidosOrderListExecutor();
    }
    this.createFrom = new PedidosCreateForm();
    this.createFrom.init();

};

PedidosObject.prototype.renderOrders = function () {
    this.orderList.renderOrders();
    console.log("rend");
};
PedidosObject.prototype.loadOrders = function () {

    $.ajax({
        dataType: "json",
        url: '/api.php',
        data: {action: 'loadOrders'},
        context: this,
        success: function (data) {
            //$('#loading').hide();
            if (data.data) {
                this.orders = data.data;
                console.log(this.orders);

                this.orderList.init(data.data);
                self.renderOrders();
            }
        },
        error: handleFailedAjax
    });

};

PedidosObject.prototype.reloadOrders = function () {
    $.ajax({
        dataType: "json",
        url: '/api.php',
        data: {action: 'loadOrders'},
        context: this,
        success: function (data) {
            //$('#loading').hide();
            if (data.data) {
                this.orders = data.data;
                console.log(this.orders);
                this.orderList.init(data.data);
                self.renderOrders();
            }
        },
        error: handleFailedAjax
    });

};


function handleFailedAjax(xhr) {
    if (xhr.status == 401) {
        window.location.href = '/auth.php';
    }
}

function addInfo(type, text) {
    var infos = $("#infos");

    var button = $("<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">×</button>");
    var i = $("<i class=\"fa fa-info-circle\"></i>");

    var div = $("<div class=\"alert  alert-dismissable\"></div>");

    var typeDefault = 'alert-info';
    if (type == "warning") {
        typeDefault = "alert-warning"
    }
    if (type == "error") {
        typeDefault = "alert-danger"
    }

    if (type == "success") {
        typeDefault = "alert-success"
    }
    div.append(button);
    div.append(i);
    div.append(text);
    div.addClass(typeDefault);
    div.appendTo(infos).hide().fadeIn("slow");

}


$(document).ready(function () {
    pedidos = new PedidosObject();
    pedidos.init();
    pedidos.loadOrders();
    pedidos.renderOrders();
    pedidos.updateBalance();

    window.setInterval(function () {
        pedidos.updateBalance();
        pedidos.reloadOrders();
        pedidos.renderOrders();
    }, 10000);
});
