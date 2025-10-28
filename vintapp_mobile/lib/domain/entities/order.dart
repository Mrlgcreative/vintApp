import 'package:equatable/equatable.dart';

enum OrderStatus {
  pending,
  confirmed,
  processing,
  shipped,
  delivered,
  cancelled,
  refunded
}

enum PaymentStatus { pending, paid, failed, refunded, cancelled }

enum PaymentMethod {
  mobileMoneyMpesa,
  mobileMoneyOrange,
  mobileMoneyAirtel,
  mobileMoneyAfricell,
  mobileMoneyIllicocash,
  wallet,
  cash
}

class Order extends Equatable {
  final String id;
  final String buyerId;
  final String buyerName;
  final String sellerId;
  final String sellerName;
  final String itemId;
  final String itemName;
  final String itemImage;
  final double itemPrice;
  final String currency;
  final int quantity;
  final double totalAmount;
  final OrderStatus orderStatus;
  final PaymentStatus paymentStatus;
  final PaymentMethod? paymentMethod;
  final DateTime createdAt;
  final DateTime? updatedAt;
  final DateTime? confirmedAt;
  final DateTime? shippedAt;
  final DateTime? deliveredAt;
  final String? buyerPhone;
  final String? deliveryAddress;
  final String? deliveryNotes;
  final String? trackingNumber;
  final String? cancellationReason;
  final Map<String, dynamic>? metadata;

  const Order({
    required this.id,
    required this.buyerId,
    required this.buyerName,
    required this.sellerId,
    required this.sellerName,
    required this.itemId,
    required this.itemName,
    required this.itemImage,
    required this.itemPrice,
    required this.currency,
    required this.quantity,
    required this.totalAmount,
    required this.orderStatus,
    required this.paymentStatus,
    this.paymentMethod,
    required this.createdAt,
    this.updatedAt,
    this.confirmedAt,
    this.shippedAt,
    this.deliveredAt,
    this.buyerPhone,
    this.deliveryAddress,
    this.deliveryNotes,
    this.trackingNumber,
    this.cancellationReason,
    this.metadata,
  });

  // Getters utiles
  String get formattedTotalAmount => '$totalAmount $currency';
  String get totalAmountWithSymbol {
    switch (currency) {
      case 'USD':
        return '\$$totalAmount';
      case 'CDF':
        return '${totalAmount.toStringAsFixed(0)} FC';
      default:
        return '$totalAmount $currency';
    }
  }

  String get orderStatusText {
    switch (orderStatus) {
      case OrderStatus.pending:
        return 'En attente';
      case OrderStatus.confirmed:
        return 'Confirmée';
      case OrderStatus.processing:
        return 'En cours';
      case OrderStatus.shipped:
        return 'Expédiée';
      case OrderStatus.delivered:
        return 'Livrée';
      case OrderStatus.cancelled:
        return 'Annulée';
      case OrderStatus.refunded:
        return 'Remboursée';
    }
  }

  String get paymentStatusText {
    switch (paymentStatus) {
      case PaymentStatus.pending:
        return 'En attente';
      case PaymentStatus.paid:
        return 'Payée';
      case PaymentStatus.failed:
        return 'Échouée';
      case PaymentStatus.refunded:
        return 'Remboursée';
      case PaymentStatus.cancelled:
        return 'Annulée';
    }
  }

  String get paymentMethodText {
    if (paymentMethod == null) return 'Non spécifié';
    switch (paymentMethod!) {
      case PaymentMethod.mobileMoneyMpesa:
        return 'M-Pesa';
      case PaymentMethod.mobileMoneyOrange:
        return 'Orange Money';
      case PaymentMethod.mobileMoneyAirtel:
        return 'Airtel Money';
      case PaymentMethod.mobileMoneyAfricell:
        return 'Africell Money';
      case PaymentMethod.mobileMoneyIllicocash:
        return 'Illicocash';
      case PaymentMethod.wallet:
        return 'Portefeuille VintApp';
      case PaymentMethod.cash:
        return 'Espèces';
    }
  }

  bool get isPending => orderStatus == OrderStatus.pending;
  bool get isConfirmed => orderStatus == OrderStatus.confirmed;
  bool get isProcessing => orderStatus == OrderStatus.processing;
  bool get isShipped => orderStatus == OrderStatus.shipped;
  bool get isDelivered => orderStatus == OrderStatus.delivered;
  bool get isCancelled => orderStatus == OrderStatus.cancelled;
  bool get isRefunded => orderStatus == OrderStatus.refunded;

  bool get isPaymentPending => paymentStatus == PaymentStatus.pending;
  bool get isPaymentCompleted => paymentStatus == PaymentStatus.paid;
  bool get isPaymentFailed => paymentStatus == PaymentStatus.failed;

  bool get canBeCancelled => isPending || isConfirmed;
  bool get canBeTracked => trackingNumber != null && trackingNumber!.isNotEmpty;
  bool get hasDeliveryAddress =>
      deliveryAddress != null && deliveryAddress!.isNotEmpty;

  @override
  List<Object?> get props => [
        id,
        buyerId,
        buyerName,
        sellerId,
        sellerName,
        itemId,
        itemName,
        itemImage,
        itemPrice,
        currency,
        quantity,
        totalAmount,
        orderStatus,
        paymentStatus,
        paymentMethod,
        createdAt,
        updatedAt,
        confirmedAt,
        shippedAt,
        deliveredAt,
        buyerPhone,
        deliveryAddress,
        deliveryNotes,
        trackingNumber,
        cancellationReason,
        metadata,
      ];

  Order copyWith({
    String? id,
    String? buyerId,
    String? buyerName,
    String? sellerId,
    String? sellerName,
    String? itemId,
    String? itemName,
    String? itemImage,
    double? itemPrice,
    String? currency,
    int? quantity,
    double? totalAmount,
    OrderStatus? orderStatus,
    PaymentStatus? paymentStatus,
    PaymentMethod? paymentMethod,
    DateTime? createdAt,
    DateTime? updatedAt,
    DateTime? confirmedAt,
    DateTime? shippedAt,
    DateTime? deliveredAt,
    String? buyerPhone,
    String? deliveryAddress,
    String? deliveryNotes,
    String? trackingNumber,
    String? cancellationReason,
    Map<String, dynamic>? metadata,
  }) {
    return Order(
      id: id ?? this.id,
      buyerId: buyerId ?? this.buyerId,
      buyerName: buyerName ?? this.buyerName,
      sellerId: sellerId ?? this.sellerId,
      sellerName: sellerName ?? this.sellerName,
      itemId: itemId ?? this.itemId,
      itemName: itemName ?? this.itemName,
      itemImage: itemImage ?? this.itemImage,
      itemPrice: itemPrice ?? this.itemPrice,
      currency: currency ?? this.currency,
      quantity: quantity ?? this.quantity,
      totalAmount: totalAmount ?? this.totalAmount,
      orderStatus: orderStatus ?? this.orderStatus,
      paymentStatus: paymentStatus ?? this.paymentStatus,
      paymentMethod: paymentMethod ?? this.paymentMethod,
      createdAt: createdAt ?? this.createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
      confirmedAt: confirmedAt ?? this.confirmedAt,
      shippedAt: shippedAt ?? this.shippedAt,
      deliveredAt: deliveredAt ?? this.deliveredAt,
      buyerPhone: buyerPhone ?? this.buyerPhone,
      deliveryAddress: deliveryAddress ?? this.deliveryAddress,
      deliveryNotes: deliveryNotes ?? this.deliveryNotes,
      trackingNumber: trackingNumber ?? this.trackingNumber,
      cancellationReason: cancellationReason ?? this.cancellationReason,
      metadata: metadata ?? this.metadata,
    );
  }
}
