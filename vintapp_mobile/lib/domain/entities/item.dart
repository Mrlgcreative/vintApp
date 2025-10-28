import 'package:equatable/equatable.dart';

enum ItemStatus { active, sold, pending, inactive, reserved }

class Item extends Equatable {
  final String id;
  final String name;
  final String description;
  final double price;
  final String currency;
  final List<String> images;
  final String categoryId;
  final String categoryName;
  final String brandId;
  final String brandName;
  final String sellerId;
  final String sellerName;
  final ItemStatus status;
  final DateTime createdAt;
  final DateTime? updatedAt;
  final String? color;
  final String? size;
  final String? itemNumber;
  final bool isPersonalized;
  final bool isFeatured;
  final int viewsCount;
  final int likesCount;
  final bool isLiked; // Par l'utilisateur actuel
  final String? condition; // new, used, excellent, etc.
  final Map<String, dynamic>? metadata;

  const Item({
    required this.id,
    required this.name,
    required this.description,
    required this.price,
    required this.currency,
    required this.images,
    required this.categoryId,
    required this.categoryName,
    required this.brandId,
    required this.brandName,
    required this.sellerId,
    required this.sellerName,
    required this.status,
    required this.createdAt,
    this.updatedAt,
    this.color,
    this.size,
    this.itemNumber,
    this.isPersonalized = false,
    this.isFeatured = false,
    this.viewsCount = 0,
    this.likesCount = 0,
    this.isLiked = false,
    this.condition,
    this.metadata,
  });

  // Getters utiles
  String get mainImage => images.isNotEmpty ? images.first : '';
  bool get hasMultipleImages => images.length > 1;
  bool get isAvailable => status == ItemStatus.active;
  bool get isSold => status == ItemStatus.sold;
  bool get isReserved => status == ItemStatus.reserved;

  String get formattedPrice => '$price $currency';
  String get priceWithSymbol {
    switch (currency) {
      case 'USD':
        return '\$$price';
      case 'CDF':
        return '${price.toStringAsFixed(0)} FC';
      default:
        return '$price $currency';
    }
  }

  String get statusText {
    switch (status) {
      case ItemStatus.active:
        return 'Disponible';
      case ItemStatus.sold:
        return 'Vendu';
      case ItemStatus.pending:
        return 'En attente';
      case ItemStatus.inactive:
        return 'Inactif';
      case ItemStatus.reserved:
        return 'Réservé';
    }
  }

  bool get isNew => condition?.toLowerCase() == 'new';
  bool get isUsed => condition?.toLowerCase() == 'used';

  String get displayCondition {
    if (condition == null) return 'Non spécifié';
    switch (condition!.toLowerCase()) {
      case 'new':
        return 'Neuf';
      case 'used':
        return 'Occasion';
      case 'excellent':
        return 'Excellent état';
      case 'good':
        return 'Bon état';
      case 'fair':
        return 'État correct';
      default:
        return condition!;
    }
  }

  @override
  List<Object?> get props => [
        id,
        name,
        description,
        price,
        currency,
        images,
        categoryId,
        categoryName,
        brandId,
        brandName,
        sellerId,
        sellerName,
        status,
        createdAt,
        updatedAt,
        color,
        size,
        itemNumber,
        isPersonalized,
        isFeatured,
        viewsCount,
        likesCount,
        isLiked,
        condition,
        metadata,
      ];

  Item copyWith({
    String? id,
    String? name,
    String? description,
    double? price,
    String? currency,
    List<String>? images,
    String? categoryId,
    String? categoryName,
    String? brandId,
    String? brandName,
    String? sellerId,
    String? sellerName,
    ItemStatus? status,
    DateTime? createdAt,
    DateTime? updatedAt,
    String? color,
    String? size,
    String? itemNumber,
    bool? isPersonalized,
    bool? isFeatured,
    int? viewsCount,
    int? likesCount,
    bool? isLiked,
    String? condition,
    Map<String, dynamic>? metadata,
  }) {
    return Item(
      id: id ?? this.id,
      name: name ?? this.name,
      description: description ?? this.description,
      price: price ?? this.price,
      currency: currency ?? this.currency,
      images: images ?? this.images,
      categoryId: categoryId ?? this.categoryId,
      categoryName: categoryName ?? this.categoryName,
      brandId: brandId ?? this.brandId,
      brandName: brandName ?? this.brandName,
      sellerId: sellerId ?? this.sellerId,
      sellerName: sellerName ?? this.sellerName,
      status: status ?? this.status,
      createdAt: createdAt ?? this.createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
      color: color ?? this.color,
      size: size ?? this.size,
      itemNumber: itemNumber ?? this.itemNumber,
      isPersonalized: isPersonalized ?? this.isPersonalized,
      isFeatured: isFeatured ?? this.isFeatured,
      viewsCount: viewsCount ?? this.viewsCount,
      likesCount: likesCount ?? this.likesCount,
      isLiked: isLiked ?? this.isLiked,
      condition: condition ?? this.condition,
      metadata: metadata ?? this.metadata,
    );
  }
}
